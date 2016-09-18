<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 25.07.2016
 * Time: 5:04
 */
namespace Jungle\Util\Data\Foundation\Schema\ValueType {
	
	use Jungle\Util\Data\Foundation\Schema\ValueType;
	use Jungle\Util\Data\Foundation\Schema\ValueTypeException;

	/**
	 * Class Pattern
	 * @package Jungle\Util\Data\Foundation\Schema\ValueType
	 */
	class Pattern extends ValueType{

		/** @var  string */
		protected $pattern;

		/**
		 * Pattern constructor.
		 * @param $alias
		 * @param $pattern
		 * @param string $vartype
		 */
		public function __construct($alias, $pattern, $vartype = null){
			$this->setAlias($alias);
			$this->vartype = $vartype;
			$this->pattern = $pattern?:'string';
		}

		/**
		 * @param $evaluated_value
		 * @param array $options
		 * @return bool
		 * @throws ValueTypeException
		 */
		public function validate($evaluated_value, array $options = null){
			if(!preg_match($this->pattern, $evaluated_value)){
				throw new ValueTypeException('is not compatible "'.$this->getName().'" type');
			}
			return true;
		}

		/**
		 * @param $raw_value
		 * @param array $options
		 * @return mixed
		 */
		public function evaluate($raw_value, array $options = null){
			return $raw_value;
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

