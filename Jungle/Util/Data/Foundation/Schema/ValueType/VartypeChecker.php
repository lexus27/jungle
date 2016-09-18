<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 25.07.2016
 * Time: 4:50
 */
namespace Jungle\Util\Data\Foundation\Schema\ValueType {
	
	use Jungle\Util\Data\Foundation\Schema\ValueType;
	use Jungle\Util\Data\Foundation\Schema\ValueTypeException;
	use Jungle\Util\Value;

	/**
	 * Class VartypeChecker
	 * @package Jungle\Util\Data\Foundation\Schema\ValueType
	 */
	class VartypeChecker extends ValueType{

		/**
		 * VartypeChecker constructor.
		 * @param $aliases
		 * @param $vartype
		 */
		public function __construct($aliases, $vartype){
			$this->setAlias($aliases);
			$this->vartype = $vartype;
		}

		/**
		 * @param $evaluated_value
		 * @param array $options
		 * @return bool
		 * @throws ValueTypeException
		 */
		public function validate($evaluated_value, array $options = null){
			if(($t = gettype($evaluated_value)) !== $this->vartype){
				throw new ValueTypeException('Verification failed, value passed type: "'.$t.'", a must be "'.$this->vartype.'"');
			}
			return true;
		}


		/**
		 * @param $passed_evaluated_value
		 * @param array $options
		 * @return mixed
		 */
		public function stabilize($passed_evaluated_value,array $options = null){
			$val = $passed_evaluated_value;
			settype($val, $this->vartype);
			if($val == $passed_evaluated_value){
				return $val;
			}
			return $passed_evaluated_value;
		}

		/**
		 * @param $raw_value
		 * @param array $options
		 * @return mixed
		 */
		public function evaluate($raw_value, array $options = null){
			return Value::setVartype($raw_value, $this->vartype);
		}

		/**
		 * @param $evaluated_value
		 * @param array $options
		 * @return mixed
		 */
		public function originate($evaluated_value, array $options = null){
			return $evaluated_value;
		}
	}
}

