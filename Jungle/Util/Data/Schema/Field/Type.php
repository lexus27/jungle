<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.06.2016
 * Time: 1:37
 */
namespace Jungle\Util\Data\Schema\Field {

	/**
	 * Class Type
	 * @package Jungle\Util\Data\Schema\Field
	 */
	class Type{

		/** @var  string[]  */
		protected $aliases = [];

		/** @var  string */
		protected $vartype; // string, integer, double, boolean, object, array, function

		public function __construct($aliases){
			if(!is_array($aliases))$aliases = [$aliases];
			$this->aliases = $aliases;
		}

		/**
		 * @param $alias
		 * @return bool
		 */
		public function isType($alias){
			return in_array($alias,$this->aliases);
		}

		public function verify($native_value){

		}

		public function originate($native_value){

		}

		public function evaluate($original_value){

		}

	}
}

