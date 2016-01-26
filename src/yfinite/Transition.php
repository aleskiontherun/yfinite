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
	 * @throws TransitionException
	 * @throws TransitionGuardException
	 */
	public function apply()
	{
		$event = new TransitionEvent($this);
		$this->trigger(TransitionEvent::BEFORE_VALIDATE, $event);

		// Check the transition is valid. A TransitionException will be thrown if it's not.
		$this->validate();

		$this->trigger(TransitionEvent::BEFORE_APPLY, $event);

		// Apply the new state to the object
		$object              = $this->getObject();
		$this->_initialState = $object->getFiniteState();
		$object->setFiniteState($this->to);

		$this->trigger(TransitionEvent::AFTER_APPLY, $event);

		return true;
	}

	/**
	 * Returns a value indicating whether the transition can be applied to the stateful object.
	 * @return bool
	 * @throws TransitionException
	 * @throws TransitionGuardException
	 */
	public function validate()
	{
		$currentState = $this->getObject()->getFiniteState();

		if (!in_array($currentState, $this->from, true))
		{
			throw new TransitionException(sprintf('Invalid object state "%s" in transition "%s". Must be one of "%s".', $currentState, $this->name, implode('", "', $this->from)));
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
	 * Useful only in the {@link TransitionEvent::AFTER_APPLY} event
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
