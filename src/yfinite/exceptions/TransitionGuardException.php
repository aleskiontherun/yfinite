<?php

namespace yfinite\exceptions;

/**
 * Class TransitionGuardException
 * @package yfinite
 * @author: Aleksei Vesnin <dizeee@dizeee.ru>
 */
class TransitionGuardException extends TransitionException
{
	/**
	 * @inheritdoc
	 */
	public function getName()
	{
		return 'yFinite Transition Guard Exception';
	}
}
