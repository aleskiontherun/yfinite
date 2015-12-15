<?php

namespace yfinite;

use yii\base\Object;

/**
 * Class Transition
 * @package yfinite
 * @author: Aleksei Vesnin <dizeee@dizeee.ru>
 */
class Transition extends Object
{
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var array
	 */
	public $from;

	/**
	 * @var string
	 */
	public $stateName;

	/**
	 * @var array
	 */
	protected $errors = [];

	/**
	 * Transition constructor.
	 * @param string $name
	 * @param array $config
	 */
	public function __construct($name, $config = [])
	{
		$this->name = $name;
		parent::__construct($config);
	}

	/**
	 * @param StateMachine $machine
	 * @return mixed
	 * @throws exceptions\TransitionException
	 */
	public function applyTo(StateMachine $machine)
	{
		if (!$this->canApplyTo($machine))
		{
			throw new exceptions\TransitionException(sprintf(
				'The "%s" transition can not be applied to the "%s" state of object "%s".',
				$this->name,
				$machine->getCurrentState()->name,
				get_class($machine->getObject())
			));
		}

		$machine->getObject()->setFiniteState($this->stateName);

		return true;
	}

	/**
	 * @param StateMachine $machine
	 * @return bool
	 */
	public function canApplyTo(StateMachine $machine)
	{
		$state = $machine->getCurrentState();

		$this->errors = [];
		if (!in_array($state->name, $this->from, true))
		{
			$this->errors[] = sprintf('Invalid machine state "%s". Must be one of "%s".', $state->name, implode('", "', $this->from));
		}

		return count($this->errors) === 0;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->name;
	}
}
