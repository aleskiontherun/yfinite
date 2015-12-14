<?php

namespace yfinite;

/**
 * Class StatefulInterface
 * @package common\components\state
 * @author: Aleksei Vesnin <dizeee@dizeee.ru>
 */
interface StatefulInterface
{
	/**
	 * Gets the object state.
	 * @return string
	 */
	public function getFiniteState();

	/**
	 * Sets the object state.
	 * @param string $state
	 */
	public function setFiniteState($state);
}
