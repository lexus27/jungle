<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.02.2016
 * Time: 1:10
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\DataSet {

	/**
	 * Class DataSet
	 * @package Jungle\_DesignPatterns\ParadigmLabs\DataSet
	 */
	class DataSet extends FieldSet{


		/**
		 * @param $key
		 */
		public function unsetProperty($key){
			unset($this->properties[$key]);
		}

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasProperty($key){
			return isset($this->properties[$key]);
		}

	}
}

