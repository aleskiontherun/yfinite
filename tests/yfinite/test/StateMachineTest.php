<?php

namespace yfinite\test;

use PHPUnit_Framework_TestCase;
use yfinite\exceptions\TransitionException;
use yfinite\exceptions\TransitionGuardException;
use yfinite\StatefulInterface;
use yfinite\StateMachine;
use yfinite\Transition;

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
				'completed'   => [],
			],
			'transitions'      => [
				'start'  => [
					'from' => ['new'],
					'to'   => 'in_progress',
				],
				'finish' => [
					'from'  => ['in_progress'],
					'to'    => 'completed',
					'guard' => [$this, 'guardTest'],
				],
			],
		]);
	}

	public function testApply()
	{
		// Initial state
		static::assertEquals($this->object, $this->stateMachine->getObject());
		$this->assertState('new');
		static::assertTrue($this->stateMachine->getCurrentState()->getProperty('editable'));

		// Invalid transition
		$this->assertInvalidTransition('finish');

		// Apply valid transition
		$this->assertApplyTransition('start', 'in_progress');
		static::assertTrue($this->stateMachine->getCurrentState()->getProperty('editable'));
		$this->assertInvalidTransition('start');

		// Apply final transition with guard
		$this->assertInvalidTransition('finish');
		$this->assertState('in_progress');
		$this->object->data = 'Some Data';
		$this->assertApplyTransition('finish', 'completed');
		static::assertNotTrue($this->stateMachine->getCurrentState()->getProperty('editable'));
		$this->assertInvalidTransition('start');
		$this->assertInvalidTransition('finish');
	}

	/**
	 * @expectedException \yfinite\exceptions\TransitionException
	 */
	public function testApplyInvalidTransition()
	{
		$this->stateMachine->apply('invalid');
	}

	/**
	 * @param Transition $transition
	 * @return bool
	 * @throws TransitionGuardException
	 */
	public function guardTest(Transition $transition)
	{
		static::assertEquals($this->object, $transition->getObject());
		if ($transition->getObject()->getFiniteState() !== 'in_progress')
		{
			throw new TransitionGuardException('Invalid object state.');
		}

		return $this->object->validate();
	}

	/**
	 * @param string $state
	 */
	protected function assertState($state)
	{
		static::assertEquals($this->object->state, $state);
		static::assertEquals((string)$this->stateMachine->getCurrentState(), $state);
	}

	/**
	 * @param string $transition
	 */
	protected function assertInvalidTransition($transition)
	{
		static::assertFalse($this->stateMachine->can($transition));
		try
		{
			$this->stateMachine->apply($transition);
		}
		catch (TransitionException $e)
		{
			return;
		}
		static::fail(sprintf('TransitionException expected for transition "%s".', $transition));
	}

	protected function assertApplyTransition($transition, $state)
	{
		static::assertTrue($this->stateMachine->can($transition));
		static::assertTrue($this->stateMachine->apply($transition));
		$this->assertState($state);
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
