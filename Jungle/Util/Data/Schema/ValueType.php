<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 25.07.2016
 * Time: 4:04
 */
namespace Jungle\Util\Data\Schema {

	use Jungle\Util\Data\Validation\RuleAggregationInterface;
	use Jungle\Util\Data\Validation\RuleAggregationTrait;

	/**
	 * Class ValueType
	 * @package Jungle\Util\Data\Schema
	 */
	abstract class ValueType implements ValueTypeInterface, RuleAggregationInterface{


		use RuleAggregationTrait;

		/** @var  string */
		protected $name;

		/** @var  string[]  */
		protected $aliases = [];

		/** @var  string */
		protected $vartype;

		/** @var array  */
		protected $converter_options = [];


		/**
		 * ValueType constructor.
		 * @param null $aliases
		 * @param $vartype
		 * @param array $rules
		 */
		public function __construct($aliases = null, $vartype = null,array $rules = null){

			if(!$aliases){
				if($this->aliases){
					$name = $this->name;
					$aliases = $this->aliases;
					$this->aliases = null;
					$this->name = null;
					$this->setAlias($aliases);
					if($name){
						$this->name = $name;
					}
				}elseif($this->name){
					$name = $this->name;
					$this->name = null;
					$this->setName($name);
				}
			}else{
				$this->setAlias($aliases);
			}

			if($vartype){
				$this->vartype = $vartype;
			}

			if($rules){
				$this->rules = $rules;
			}


		}

		/**
		 * @return mixed
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			$this->name = $name;
			$this->aliases[$name] = true;
			return $this;
		}

		/**
		 * @param $vartype
		 * @return $this
		 */
		public function setVartype($vartype){
			$this->vartype = $vartype;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getVartype(){
			return $this->vartype;
		}

		/**
		 * @param array $converter_options
		 * @return $this
		 */
		public function setConverterOptions(array $converter_options = []){
			$this->converter_options = $converter_options;
			return $this;
		}

		/**
		 * @return array
		 */
		public function getConverterOptions(){
			return $this->converter_options;
		}

		/**
		 * @param $alias
		 * @return $this
		 */
		public function setAlias($alias){
			$this->aliases = array_fill_keys(is_array($alias)?$alias:[$alias], true);
			$this->name = key($this->aliases);
			return $this;
		}

		/**
		 * @param $alias
		 * @return $this
		 */
		public function addAlias($alias){
			$this->aliases[$alias] = true;
			if(!$this->name){
				$this->name = $alias;
			}
			return $this;
		}

		/**
		 * @param $alias
		 * @return $this
		 */
		public function removeAlias($alias){
			unset($this->aliases[$alias]);
			if($this->name === $alias){
				$this->name = key($this->aliases);
			}
			return $this;
		}

		/**
		 * @param $alias
		 * @return bool
		 */
		public function hasAlias($alias){
			return isset($this->aliases[$alias]);
		}


		/**
		 * @param $passed_evaluated_value
		 * @param array $options
		 * @return mixed
		 */
		public function stabilize($passed_evaluated_value,array $options = null){
			return $passed_evaluated_value;
		}

		/**
		 * @param $value
		 * @param array $options
		 * @return bool
		 */
		public function validate($value,array $options = null){
			return $this->check($value);
		}

	}
}

