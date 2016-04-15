<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 5:50
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections\Concretes {

	/**
	 * Options Getter`s
	 * Interface IOptionContainerReader
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections
	 */
	interface IOptionsAwareRead{

		/**
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		public function getOption($key, $value);

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasOption($key);

	}
}

