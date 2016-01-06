<?php

namespace yfinite\examples\hierarchy;

use yfinite\StatefulInterface;

/**
 * Example Class Person
 * @author: Aleksei Vesnin <dizeee@dizeee.ru>
 */
class Person implements StatefulInterface
{

	public $unemployed = false;

	private $state;

	/**
	 * @inheritdoc
	 */
	public function getFiniteState()
	{
		return $this->state;
	}

	/**
	 * @inheritdoc
	 */
	public function setFiniteState($state)
	{
		$this->state = $state;
	}

	public function brushTeeth()
	{
		echo "Brushing teeth...\n";
	}
}
