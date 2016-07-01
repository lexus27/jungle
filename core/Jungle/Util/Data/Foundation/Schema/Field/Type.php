<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.06.2016
 * Time: 1:37
 */
namespace Jungle\Util\Data\Foundation\Schema\Field {

	/**
	 * Class Type
	 * @package Jungle\Util\Data\Foundation\Schema\Field
	 */
	class Type{

		/**
		 * @param $name
		 * @return bool
		 */
		public function isName($name){
			return in_array($name,$this->name);
		}

		/** @var  string[]  */
		protected $name = [];

		/** @var  string */
		protected $vartype; // string, integer, double, boolean, object, array, function


		public function verify($native_value){

		}

		public function originate($native_value){

		}

		public function evaluate($original_value){
			
		}

	}
}

