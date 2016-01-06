<?php

namespace yfinite\examples\hierarchy;
use yfinite\TransitionEvent;

/**
 * Example Class PersonStates_sleeping
 * @author: Aleksei Vesnin <dizeee@dizeee.ru>
 */
class PersonStatesSleeping extends PersonStates
{
	public static $states =
		[
			self::STATE_SLEEPING_NON_REM => [],
			self::STATE_SLEEPING_REM     => [],
		];

	public static $transitions =
		[
			self::TRANSITION_GO_TO_SLEEP    =>
				[
					'from'                         => [self::STATE_AWAKE_AT_HOME],
					'to'                           => self::STATE_SLEEPING_NON_REM,
					'on yfinite.transition.before' => [self::CLASS, 'beforeSleep'],
				],
			self::TRANSITION_START_DREAMING =>
				[
					'from' => [self::STATE_SLEEPING_NON_REM],
					'to'   => self::STATE_SLEEPING_REM,
				],
			self::TRANSITION_STOP_DREAMING  =>
				[
					'from' => [self::STATE_SLEEPING_REM],
					'to'   => self::STATE_SLEEPING_NON_REM,
				],
		];

	public function beforeSleep(TransitionEvent $event)
	{
		/** @var Person $person */
		$person = $event->transition->getObject();
		$person->brushTeeth();
	}
}
