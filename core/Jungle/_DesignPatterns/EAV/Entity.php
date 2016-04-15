<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 23.02.2016
 * Time: 7:08
 */
namespace Jungle\_DesignPatterns\EAV {

	/**
	 * Class Entity
	 * @package Jungle\_DesignPatterns\EAV
	 */
	class Entity{

		protected $values = [];

		public function addValue(Value $value){
			$this->values[] = $value;
		}

	}
}

