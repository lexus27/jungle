<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 5:56
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections {

	/**
	 * Interface ICountCollected
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections
	 */
	interface ICountCollected extends \Countable{

		/**
		 * @param callable|null $collector count collected by collector rule
		 * @return mixed
		 */
		public function count(callable $collector = null);

	}
}

