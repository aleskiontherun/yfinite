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
	const
		EVENT_BEFORE_TRANSITION = 'yfinite.transition.before',
		EVENT_AFTER_TRANSITION = 'yfinite.transition.after';

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
	 * @return StatefulInterface
	 */
	public function getObject()
	{
		return $this->_object;
	}

	/**
	 * @return State
	 */
	public function getCurrentState()
	{
		return $this->_currentState;
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
		$this->_currentState = $this->getState($transition->stateName);

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

	public static function configClass(&$config, $default)
	{
		if (isset($config['class']))
		{
			$class = $config['class'];
			unset($config['class']);
		}
		else
		{
			$class = $default;
		}
		return $class;
	}

}
