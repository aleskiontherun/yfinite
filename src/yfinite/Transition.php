<?php

namespace yfinite;

use yfinite\exceptions\TransitionException;
use yfinite\exceptions\TransitionGuardException;
use yii\base\Component;

/**
 * Class Transition
 * @package yfinite
 * @author: Aleksei Vesnin <dizeee@dizeee.ru>
 */
class Transition extends Component
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
	public $to;

	/**
	 * @var callable
	 */
	public $guard;

	/**
	 * @var string
	 */
	private $_initialState;

	/**
	 * @var StatefulInterface
	 */
	private $_object;

	/**
	 * Transition constructor.
	 * @param string $name
	 * @param StatefulInterface $object
	 * @param array $config
	 */
	public function __construct($name, StatefulInterface $object, $config = [])
	{
		$this->name    = $name;
		$this->_object = $object;
		parent::__construct($config);
	}

	/**
	 * Applies the transition to the stateful object.
	 * @return bool
	 * @throws exceptions\TransitionException
	 */
	public function apply()
	{
		// Check the transition is valid. A TransitionException will be thrown if it's not.
		$this->validate();

		$object = $this->getObject();

		$event = new TransitionEvent($this);
		$this->trigger(TransitionEvent::BEFORE, $event);

		// Apply the new state to the object
		$object->setFiniteState($this->to);
		$this->trigger(TransitionEvent::AFTER, $event);

		return true;
	}

	/**
	 * Returns a value indicating whether the transition can be applied to the stateful object.
	 * @return bool
	 * @throws TransitionException
	 */
	public function validate()
	{
		$object = $this->getObject();
		// Store the object initial state
		$this->_initialState = $object->getFiniteState();

		if (!in_array($this->_initialState, $this->from, true))
		{
			throw new TransitionException(sprintf('Invalid object state "%s" in transition "%s". Must be one of "%s".', $this->_initialState, $this->name, implode('", "', $this->from)));
		}
		elseif (is_callable($this->guard) && call_user_func($this->guard, $this) !== true)
		{
			throw new TransitionGuardException(sprintf('Transition guard didn\'t return true in transition "%s".', $this->name));
		}
		return true;
	}

	/**
	 * @return StatefulInterface
	 */
	public function getObject()
	{
		return $this->_object;
	}

	/**
	 * Returns the object state before the transition
	 * @return string
	 */
	public function getInitialState()
	{
		return $this->_initialState;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->name;
	}
}
