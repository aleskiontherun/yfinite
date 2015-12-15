<?php

namespace yfinite;

use Yii;
use yii\base\Component;

/**
 * Class StateMachine
 * @package yfinite
 * @author: Aleksei Vesnin <dizeee@dizeee.ru>
 */
class StateMachine extends Component
{

	public $name = 'default';

	public $defaultStateClass = State::CLASS;
	public $defaultTransitionClass = Transition::CLASS;

	public $defaultStateName;
	public $states;
	public $transitions;

	private $_stateInstances = [];
	private $_transitionInstances = [];

	/**
	 * @var StatefulInterface
	 */
	private $_object;

	/**
	 * @var State
	 */
	private $_currentState;

	/**
	 * StateMachine constructor.
	 * @param StatefulInterface $object
	 * @param array $config
	 */
	public function __construct(StatefulInterface $object, $config = [])
	{
		$this->_object = $object;
		parent::__construct($config);
	}

	/**
	 * Initializes the state machine setting default object state if needed and retrieving current object state.
	 * @throws exceptions\StateException
	 */
	public function init()
	{
		if ($this->defaultStateName && !$this->getObject()->getFiniteState())
		{
			$this->getObject()->setFiniteState($this->defaultStateName);
		}
		$this->_currentState = $this->getState($this->getObject()->getFiniteState());
		parent::init();
	}

	/**
	 * Returns the stateful object.
	 * @return StatefulInterface
	 */
	public function getObject()
	{
		return $this->_object;
	}

	/**
	 * Returns the current object state.
	 * @return State
	 */
	public function getCurrentState()
	{
		return $this->_currentState;
	}

	/**
	 * Applies a transition to the stateful object.
	 * @param string $transitionName
	 * @param array $parameters
	 * @return mixed
	 * @throws exceptions\StateException
	 * @throws exceptions\TransitionException
	 */
	public function apply($transitionName, array $parameters = array())
	{
		$transition = $this->getTransition($transitionName);

		$event = new TransitionEvent($transition);

		$this->trigger(TransitionEvent::BEFORE_TRANSITION, $event);

		$result              = $transition->applyTo($this);
		$this->_currentState = $this->getState($transition->stateName);

		$this->trigger(TransitionEvent::AFTER_TRANSITION, $event);

		return $result;
	}

	/**
	 * Returns a value indicating whether the specified transition can be applied to the stateful object.
	 * @param string $transitionName
	 * @return bool
	 * @throws exceptions\TransitionException
	 */
	public function can($transitionName)
	{
		return $this->getTransition($transitionName)->canApplyTo($this);
	}

	/**
	 * Returns a transition instance.
	 * @param string $name
	 * @return Transition
	 * @throws exceptions\TransitionException
	 */
	public function getTransition($name)
	{
		// Check if the transition is available
		if (!isset($this->transitions[$name]))
		{
			throw new exceptions\TransitionException(sprintf(
				'Unable to find a transition "%s" on object "%s".',
				$name,
				get_class($this->getObject())
			));
		}

		// Initialize the transition object
		if (!isset($this->_transitionInstances[$name]))
		{
			$config = array_merge(['class' => $this->defaultTransitionClass], $this->transitions[$name]);

			$this->_transitionInstances[$name] = Yii::createObject($config, [$name]);
		}

		return $this->_transitionInstances[$name];
	}

	/**
	 * Returns a state instance.
	 * @param string $name
	 * @return State
	 * @throws exceptions\StateException
	 */
	public function getState($name)
	{
		// Check if the state is available
		if (!isset($this->states[$name]))
		{
			throw new exceptions\StateException(sprintf(
				'Unable to find a state "%s" on object "%s".',
				$name,
				get_class($this->getObject())
			));
		}

		// Initialize the state object
		if (!isset($this->_stateInstances[$name]))
		{
			$config = array_merge(['class' => $this->defaultStateClass], $this->states[$name]);

			$this->_stateInstances[$name] = Yii::createObject($config, [$name]);
		}

		return $this->_stateInstances[$name];
	}

}
