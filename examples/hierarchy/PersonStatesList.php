<?php

namespace yfinite\examples\hierarchy;

use ArrayAccess;
use BadMethodCallException;

/**
 * Example Class YearStatesList
 * @author: Aleksei Vesnin <dizeee@dizeee.ru>
 */
class PersonStatesList implements ArrayAccess
{
	/**
	 * @inheritdoc
	 */
	public function offsetExists($offset)
	{
		$class = PersonStates::getConfigClass($offset);
		return isset($class::$states[$offset]);
	}

	/**
	 * @inheritdoc
	 */
	public function offsetGet($offset)
	{
		$class = PersonStates::getConfigClass($offset);
		return isset($class::$states[$offset]) ? $class::$states[$offset] : null;
	}

	/**
	 * @inheritdoc
	 */
	public function offsetSet($offset, $value)
	{
		throw new BadMethodCallException(sprintf('%s do not support data modification', __CLASS__));
	}

	/**
	 * @inheritdoc
	 */
	public function offsetUnset($offset)
	{
		throw new BadMethodCallException(sprintf('%s do not support data modification', __CLASS__));
	}
}
