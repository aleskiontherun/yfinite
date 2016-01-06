<?php

namespace yfinite\examples\hierarchy;

/**
 * Example Class PersonStates_awake
 * @author: Aleksei Vesnin <dizeee@dizeee.ru>
 */
class PersonStatesAwake extends PersonStates
{

	public static $states =
		[
			self::STATE_AWAKE_AT_HOME => [],
			self::STATE_AWAKE_AT_WORK => [],
		];

	public static $transitions =
		[
			self::TRANSITION_WAKE_UP    =>
				[
					'from' => [self::STATE_SLEEPING_REM, self::STATE_SLEEPING_NON_REM],
					'to'   => self::STATE_AWAKE_AT_HOME,
				],
			self::TRANSITION_GO_TO_WORK =>
				[
					'from' => [self::STATE_AWAKE_AT_HOME],
					'to'   => self::STATE_AWAKE_AT_WORK,
				],
			self::TRANSITION_GO_HOME    =>
				[
					'from' => [self::STATE_AWAKE_AT_WORK],
					'to'   => self::STATE_AWAKE_AT_HOME,
				],
		];

}
