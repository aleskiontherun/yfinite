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
		BEFORE_VALIDATE = 'yfinite.transition.beforeValidate',
		BEFORE_APPLY = 'yfinite.transition.beforeApply',
		AFTER_APPLY = 'yfinite.transition.afterApply';

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
