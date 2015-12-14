<?php

namespace yfinite;

use yii\base\Event;

/**
 * Class TransitionEvent
 * @package common\components\state
 * @author: Aleksei Vesnin <dizeee@dizeee.ru>
 */
class TransitionEvent extends Event
{

	/**
	 * @var Transition
	 */
	public $transition;

	public function __construct(Transition $transition, $config = [])
	{
		$this->transition = $transition;
		parent::__construct($config);
	}
}
