<?php

namespace yfinite\examples\hierarchy;

use yfinite\StateMachine;

/**
 * Example Class YearStates
 * @author: Aleksei Vesnin <dizeee@dizeee.ru>
 */
class PersonStates
{
	const DEFAULT_STATE = self::STATE_AWAKE_AT_HOME;

	const
		STATE_SLEEPING_REM = 'sleeping.rem',
		STATE_SLEEPING_NON_REM = 'sleeping.non_rem',

		STATE_AWAKE_AT_HOME = 'awake.at_home',
		STATE_AWAKE_AT_WORK = 'awake.at_work';

	const
		TRANSITION_GO_TO_SLEEP = 'sleeping.start',
		TRANSITION_START_DREAMING = 'sleeping.start_dreaming',
		TRANSITION_STOP_DREAMING = 'sleeping.stop_dreaming',

		TRANSITION_WAKE_UP = 'awake.start',
		TRANSITION_GO_TO_WORK = 'awake.go_to_work',
		TRANSITION_GO_HOME = 'awake.go_home';

	public static $states = [];
	public static $transitions = [];

	/**
	 * @param Person $person
	 * @return StateMachine
	 */
	public static function createStateMachine(Person $person)
	{
		return new StateMachine($person, [
			'defaultStateName' => self::DEFAULT_STATE,
			'states'           => new PersonStatesList(),
			'transitions'      => new PersonTransitionsList(),
		]);
	}

	/**
	 * @param string $path
	 * @return static
	 * @throws InvalidPathException
	 */
	public static function getConfigClass($path)
	{
		$class = self::CLASS . ucfirst(substr($path, 0, strpos($path, '.')));
		if (!class_exists($class))
		{
			throw new InvalidPathException(sprintf('Invalid state or transition path "%s": class %s not found.', $path, $class));
		}
		return $class;
	}
}
