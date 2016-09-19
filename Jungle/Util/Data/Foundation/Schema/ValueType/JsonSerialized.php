<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 25.07.2016
 * Time: 4:17
 */
namespace Jungle\Util\Data\Foundation\Schema\ValueType {
	
	use Jungle\Util\Data\Foundation\Schema\ValueType;
	use Jungle\Util\Data\Foundation\Schema\ValueTypeException;

	/**
	 * Class JsonSerialized
	 * @package Jungle\Util\Data\Foundation\Schema\ValueType
	 */
	class JsonSerialized extends ValueType{

		/** @var array  */
		protected $aliases = ['json'];

		/** @var string  */
		protected $vartype = 'mixed';

		/**
		 * @param $evaluated_value
		 * @param array $options
		 * @return bool
		 * @throws ValueTypeException
		 */
		public function validate($evaluated_value, array $options = null){
			if(is_array($evaluated_value) || $evaluated_value instanceof \JsonSerializable || $evaluated_value instanceof \stdClass){
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
			return json_decode($raw_value);
		}

		/**
		 * @param $evaluated_value
		 * @param array $options
		 * @return mixed
		 */
		public function originate($evaluated_value, array $options = null){
			return json_encode($evaluated_value);
		}

	}
}

