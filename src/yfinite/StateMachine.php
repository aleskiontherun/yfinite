<?php

namespace yfinite;

use yii\base\Component;

/**
 * Class StateMachine
 * @package common\components\state
 * @author: Aleksei Vesnin <dizeee@dizeee.ru>
 */
class StateMachine extends Component
{
	const
		EVENT_BEFORE_TRANSITION = 'state.transition.before',
		EVENT_AFTER_TRANSITION = 'state.transition.after';

	public $name = 'default';

	public $defaultStateClass = State::CLASS;
	public $defaultTransitionClass = Transition::CLASS;

	public $defaultStateName;
	public $states;
	public $transitions;

	/**
	 * @var StatefulInterface
	 */
	private $object;

	/**
	 * @var State
	 */
	private $currentState;

	/**
	 * StateMachine constructor.
	 * @param StatefulInterface $object
	 * @param array $config
	 */
	public function __construct(StatefulInterface $object, $config = [])
	{
		$this->object = $object;
		parent::__construct($config);
	}

	public function init()
	{
		if ($this->defaultStateName && !$this->getObject()->getFiniteState())
		{
			$this->getObject()->setFiniteState($this->defaultStateName);
		}
		$this->currentState = $this->getState($this->getObject()->getFiniteState());
		parent::init();
	}

	/**
	 * @return StatefulInterface
	 */
	public function getObject()
	{
		return $this->object;
	}

	/**
	 * @return State
	 */
	public function getCurrentState()
	{
		return $this->currentState;
	}

	/**
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

		$this->trigger(self::EVENT_BEFORE_TRANSITION, $event);

		$returnValue = $transition->applyTo($this);
		//$this->stateAccessor->setState($this->object, $transition->stateName);
		$this->currentState = $this->getState($transition->stateName);

		$this->trigger(self::EVENT_AFTER_TRANSITION, $event);

		return $returnValue;
	}

	/**
	 * @param string $transitionName
	 * @return bool
	 * @throws exceptions\TransitionException
	 */
	public function can($transitionName)
	{
		return $this->getTransition($transitionName)->canApplyTo($this);
	}

	/**
	 * @param string $name
	 * @return Transition
	 * @throws exceptions\TransitionException
	 */
	public function getTransition($name)
	{
		// Check if the transition is available
		if (!array_key_exists($name, $this->transitions))
		{
			throw new exceptions\TransitionException(sprintf(
				'Unable to find a transition "%s" on object "%s".',
				$name,
				get_class($this->getObject())
			));
		}

		// Initialize the transition object
		if (!$this->transitions[$name] instanceof $this->defaultTransitionClass)
		{
			$this->transitions[$name] = new $this->defaultTransitionClass($name, $this->transitions[$name]);
		}

		return $this->transitions[$name];
	}

	/**
	 * @param string $name
	 * @return State
	 * @throws exceptions\StateException
	 */
	public function getState($name)
	{
		// Check if the transition is available
		if (!array_key_exists($name, $this->states))
		{
			throw new exceptions\StateException(sprintf(
				'Unable to find a state "%s" on object "%s".',
				$name,
				get_class($this->getObject())
			));
		}

		// Initialize the transition object
		if (!$this->states[$name] instanceof $this->defaultStateClass)
		{
			$this->states[$name] = new $this->defaultStateClass($name, $this->states[$name]);
		}

		return $this->states[$name];
	}

}
