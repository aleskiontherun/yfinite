<?php

namespace yfinite\examples\hierarchy;
use yii\base\Exception;

/**
 * Example Class InvalidPathException
 * @author: Aleksei Vesnin <dizeee@dizeee.ru>
 */
class InvalidPathException extends Exception
{
	public function getName()
	{
		return 'Invalid Product State Path Exception';
	}
}
