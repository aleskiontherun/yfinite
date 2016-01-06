<?php

namespace yfinite\exceptions;

use yii\base\Exception;

/**
 * Class TransitionException
 * @package yfinite
 * @author: Aleksei Vesnin <dizeee@dizeee.ru>
 */
class TransitionException extends Exception
{
	/**
	 * @inheritdoc
	 */
	public function getName()
	{
		return 'yFinite Transition Exception';
	}
}
