<?php

namespace yfinite;

use yii\base\Event;

/**
 * Class TransitionEvent
 * @package yfinite
 * @author: Aleksei Vesnin <dizeee@dizeee.ru>
 */
class TransitionEvent extends Event
{
	const
		BEFORE_TRANSITION = 'yfinite.transition.before',
		AFTER_TRANSITION = 'yfinite.transition.after';

	/**
	 * @var Transition
	 */
	public $transition;

	/**
	 * TransitionEvent constructor.
	 * @param Transition $transition
	 * @param array $config
	 */
	public function __construct(Transition $transition, $config = [])
	{
		$this->transition = $transition;
		parent::__construct($config);
	}
}
