<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 25.07.2016
 * Time: 4:14
 */
namespace Jungle\Util\Data\Schema\ValueType {
	
	use Jungle\Util\Data\Schema\ValueType;
	use Jungle\Util\Data\Schema\ValueTypeException;

	/**
	 * Class Serialized
	 * @package Jungle\Util\Data\Schema\ValueType
	 */
	class Serialized extends ValueType{

		/** @var array  */
		protected $aliases = ['serialized','variable','mixed'];

		/** @var string  */
		protected $vartype = 'mixed';

		/**
		 * @param $evaluated_value
		 * @param array $options
		 * @return bool
		 * @throws ValueTypeException
		 */
		public function validate($evaluated_value, array $options = null){
			if(!is_object($evaluated_value) || $evaluated_value instanceof \Serializable){
				throw new ValueTypeException('Not Supported Value');
			}
			return $this->check($evaluated_value);
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

