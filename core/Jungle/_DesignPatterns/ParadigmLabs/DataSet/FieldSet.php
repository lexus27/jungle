<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.02.2016
 * Time: 1:13
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\DataSet {

	/**
	 * Class FieldSet
	 * @package Jungle\_DesignPatterns\ParadigmLabs\DataSet
	 */
	class FieldSet{

		/**
		 * @var mixed[]
		 */
		protected $properties;

		/**
		 * @param $field
		 * @param $value
		 */
		public function set($field,$value){
			$this->properties[$field] = $value;
		}

		/**
		 * @param $field
		 * @return mixed
		 */
		public function get($field){
			return $this->properties[$field];
		}

	}
}

