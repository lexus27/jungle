<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 25.07.2016
 * Time: 4:14
 */
namespace Jungle\Util\Data\Foundation\Schema\ValueType {
	
	use Jungle\Util\Data\Foundation\Schema\ValueType;

	/**
	 * Class Serialized
	 * @package Jungle\Util\Data\Foundation\Schema\ValueType
	 */
	class Serialized extends ValueType{

		/** @var array  */
		protected $aliases = ['serialized','variable'];

		/** @var string  */
		protected $vartype = 'mixed';

		/**
		 * @param $evaluated_value
		 * @param array $options
		 * @return bool
		 */
		public function verify($evaluated_value, array $options = null){
			return !is_object($evaluated_value) || $evaluated_value instanceof \Serializable;
		}

		/**
		 * @param $raw_value
		 * @param array $options
		 * @return mixed
		 */
		public function evaluate($raw_value, array $options = null){
			return unserialize($raw_value);
		}

		/**
		 * @param $evaluated_value
		 * @param array $options
		 * @return mixed
		 */
		public function originate($evaluated_value, array $options = null){
			return serialize($evaluated_value);
		}

	}
}

