<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.09.2016
 * Time: 14:52
 */
namespace Jungle\Util\Data\Foundation\Schema\ValueType {
	
	use Jungle\Util\Data\Foundation\Schema\ValueType;

	/**
	 * Class Vartype
	 * @package Jungle\Util\Data\Foundation\Schema\ValueType
	 */
	class Vartype extends ValueType{

		/**
		 * @param $raw_value
		 * @param array $options
		 * @return mixed
		 */
		public function evaluate($raw_value, array $options = null){
			settype($raw_value, $this->vartype);
			return $raw_value;
		}

		/**
		 * @param $evaluated_value
		 * @param array $options
		 * @return mixed|string
		 */
		public function originate($evaluated_value, array $options = null){
			return "{$evaluated_value}";
		}
	}
}

