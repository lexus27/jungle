<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 5:50
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections\Concretes {

	/**
	 * Options Setter
	 * Interface IOptionsAwareWrite
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections
	 */
	interface IOptionsAwareWrite{

		/**
		 * @param $key
		 * @return mixed
		 */
		public function removeOption($key);

		/**
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		public function setOption($key, $value);

	}
}

