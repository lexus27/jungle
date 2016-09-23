<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 18:53
 */
namespace Jungle\Util\Data\Schema {

	/**
	 * Class Field
	 * @package Jungle\Util\Data\Schema
	 */
	abstract class Field implements FieldInterface, Indexed\FieldInterface{

		/** @var  Schema */
		protected $schema;

		/** @var  string */
		protected $name;

		/** @var  string */
		protected $type = 'string';

		/** @var  null */
		protected $default = null;

		/** @var  bool */
		protected $nullable = false;

		/** @var array  */
		protected $options = [];

		/**
		 * Field constructor.
		 * @param $name
		 * @param string $type
		 */
		public function __construct($name, $type = null){
			$this->name = $name;
			$this->type = $type?:'string';
		}

		/**
		 * @param Schema $schema
		 * @return $this
		 */
		public function setSchema(Schema $schema){
			if($this->schema !== $schema){
				$this->schema = $schema;
			}
			return $this;
		}

		/**
		 * @return Schema
		 */
		public function getSchema(){
			return $this->schema;
		}

		/**
		 * @param array $options
		 * @param bool|false $merge
		 * @return $this
		 */
		public function setOptions(array $options, $merge = false){
			$this->options = $merge?array_replace($this->options,$options):$options;
			return $this;
		}

		/**
		 * @return array
		 */
		public function getOptions(){
			return $this->options;
		}

		/**
		 * @param $key
		 * @param null $default
		 * @return null
		 */
		public function getOption($key, $default = null){
			return isset($this->options[$key])?$this->options[$key]:$default;
		}

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasOption($key){
			return isset($this->options[$key]);
		}


		/**
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @return string
		 */
		public function getType(){
			return $this->type;
		}

		/**
		 * @return mixed
		 */
		public function getDefault(){
			return $this->default;
		}

		/**
		 * @return bool
		 */
		public function isNullable(){
			return $this->nullable;
		}

		/**
		 * @return bool
		 */
		public function isDefaultNull(){
			return $this->default === null && $this->nullable;
		}


		/**
		 * @return bool
		 */
		public function isPrimary(){
			return $this->schema->isPrimaryField($this->name);
		}

		/**
		 * @return bool
		 */
		public function isUnique(){
			return $this->schema->isPrimaryField($this->name);
		}


		/**
		 * @param $native_value
		 * @return mixed
		 */
		public function originate($native_value){
			return $native_value;
		}

		/**
		 * @param $originality_value
		 * @return mixed
		 */
		public function evaluate($originality_value){
			if($originality_value === null){
				return $originality_value;
			}
			if($this->type){
				settype($originality_value,$this->type);
			}
			return $originality_value;
		}

		/**
		 * @param $native_value
		 * @return bool
		 */
		public function validate($native_value){
			if($native_value === null && $this->isNullable()){
				return true;
			}
			$type = gettype($native_value);
			return $type === $this->type;
		}

		/**
		 * @param $value
		 * @return mixed
		 */
		public function stabilize($value){
			if($value === null && $this->isNullable()){
				return $value;
			}
			if($this->type){
				settype($value,$this->type);
			}
			return $value;
		}


	}
}

