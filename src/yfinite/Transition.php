<?php

namespace yfinite;

use yfinite\exceptions\TransitionException;
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
		if (!$this->validate())
		{
			return false;
		}

		// TODO: Store object initial state?

		$object = $this->getObject();
		$event  = new TransitionEvent($this);

		$this->trigger(TransitionEvent::BEFORE, $event);

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
		$state  = $object->getFiniteState();

		if (!in_array($state, $this->from, true))
		{
			//throw new TransitionException(sprintf('Invalid object state "%s". Must be one of "%s".', $state, implode('", "', $this->from)));
			return false;
		}
		elseif (is_callable($this->guard) && call_user_func($this->guard, $object) === false)
		{
			return false;
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
	 * @return string
	 */
	public function __toString()
	{
		return $this->name;
	}
}
