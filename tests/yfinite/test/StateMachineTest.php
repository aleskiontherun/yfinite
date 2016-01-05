<?php

namespace yfinite\test;

use PHPUnit_Framework_TestCase;
use yfinite\StatefulInterface;
use yfinite\StateMachine;

class StateMachineTest extends PHPUnit_Framework_TestCase
{

	/** @var StatefulObject */
	protected $object;
	/** @var StateMachine */
	protected $stateMachine;

	protected function setUp()
	{
		$this->object       = new StatefulObject;
		$this->stateMachine = new StateMachine($this->object, [
			'defaultStateName' => 'new',
			'states'           => [
				'new'         => ['properties' => ['editable' => true]],
				'in_progress' => ['properties' => ['editable' => true]],
				'complete'    => [],
			],
			'transitions'      => [
				'start'  => [
					'from' => ['new'],
					'to'   => 'in_progress',
				],
				'finish' => [
					'from'  => ['in_progress'],
					'to'    => 'complete',
					'guard' => function (StatefulObject $object)
					{
						return $object->validate();
					}
				],
			],
		]);
	}

	public function testApply()
	{
		// Initial state
		static::assertEquals($this->object, $this->stateMachine->getObject());
		static::assertEquals($this->object->state, 'new');
		static::assertTrue($this->stateMachine->can('start'));
		static::assertFalse($this->stateMachine->can('finish'));
		static::assertTrue($this->stateMachine->getCurrentState()->getProperty('editable'));

		// Invalid transitions
		static::assertFalse($this->stateMachine->apply('finish'));
		$this->assertState('new');

		// Apply valid transition
		static::assertTrue($this->stateMachine->apply('start'));
		$this->assertState('in_progress');
		static::assertTrue($this->stateMachine->getCurrentState()->getProperty('editable'));
		static::assertFalse($this->stateMachine->can('start'));

		// Apply final transition with guard
		static::assertFalse($this->stateMachine->can('finish'));
		static::assertFalse($this->stateMachine->apply('finish'));
		$this->assertState('in_progress');
		$this->object->data = 'Some Data';
		static::assertTrue($this->stateMachine->can('finish'));
		static::assertTrue($this->stateMachine->apply('finish'));
		$this->assertState('complete');
		static::assertNotTrue($this->stateMachine->getCurrentState()->getProperty('editable'));
		static::assertFalse($this->stateMachine->can('start'));
		static::assertFalse($this->stateMachine->can('finish'));
	}

	/**
	 * @expectedException \yfinite\exceptions\TransitionException
	 */
	public function testApplyInvalidTransition()
	{
		$this->stateMachine->apply('invalid');
	}

	/**
	 * @expectedException \yfinite\exceptions\TransitionException
	 */
	public function testCanInvalidTransition()
	{
		$this->stateMachine->can('invalid');
	}

	/**
	 * @param string $state
	 */
	protected function assertState($state)
	{
		static::assertEquals($this->object->state, $state);
		static::assertEquals((string)$this->stateMachine->getCurrentState(), $state);
	}
}

class StatefulObject implements StatefulInterface
{

	public $state;

	public $data;

	/**
	 * @inheritdoc
	 */
	public function getFiniteState()
	{
		return $this->state;
	}

	/**
	 * @inheritdoc
	 */
	public function setFiniteState($state)
	{
		$this->state = $state;
	}

	public function validate()
	{
		return $this->data !== null;
	}
}
