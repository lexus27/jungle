<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 0:52
 */
namespace Jungle\Data\Storage\Db {

	use Jungle\Data\Storage\Db\Structure\Column;
	use Jungle\Util\System;

	/**
	 * Class Sql
	 * @package Jungle\Data\Storage\Db
	 *
	 *
	 * TODO Special Placeholder helping
	 *
	 *
	 */
	class Sql{

		/** @var string  */
		protected $sql = '';

		/** @var array  */
		protected $bindings = [];

		/** @var bool  */
		protected $completed = false;

		/** @var array  */
		protected $data_types = [];

		/** @var int  */
		protected $default_data_type = Column::BIND_PARAM_STR;

		/** @var string  */
		protected $default_delimiter = ' ';


		/**
		 * @param string $delimiter
		 * @return $this
		 */
		public function setDefaultDelimiter($delimiter = ' '){
			$this->default_delimiter = $delimiter;
			return $this;
		}

		/**
		 * @param $sql
		 * @param string $delimiter
		 * @param array|null $bindings
		 * @param array|null $dataTypes
		 * @return $this
		 */
		public function push($sql, $delimiter = null, array $bindings = null,array $dataTypes = null){
			if($this->isCompleted()){
				throw new \LogicException('Sql is completed!');
			}
			if($delimiter===null)$delimiter = $this->default_delimiter;
			$this->sql = ($this->sql?rtrim($this->sql,"$delimiter\r\n\t\0\x0B ") . $delimiter:'') . $sql;
			$this->_merge($bindings,$dataTypes);
			return $this;
		}

		/**
		 * @param $bindings
		 * @param $dataTypes
		 */
		protected function _merge($bindings, $dataTypes){
			if($bindings){
				foreach($bindings as $index => $bind){
					if(is_int($index)){
						$this->bindings[] = $bind;
					}else{
						$this->bindings[$index] = $bind;
					}
				}
				$generalDataType = false;
				if(!is_array($dataTypes)){
					if(!is_null($dataTypes)){
						$generalDataType = $dataTypes;
					}
					$dataTypes = [];
				}
				foreach($bindings as $i => $b){
					if($generalDataType!==false){
						$dataTypes[$i] = $generalDataType;
					}elseif(!isset($dataTypes[$i])){
						$dataTypes[$i] = null;
					}
				}
			}
			if($dataTypes){
				foreach($dataTypes as $index => $type){
					if(is_int($index)){
						$this->data_types[] = $type;
					}else{
						$this->data_types[$index] = $type;
					}
				}
			}
		}


		/**
		 * @return string
		 */
		public function getSql(){
			return $this->sql;
		}

		/**
		 * @return $this
		 */
		public function ending(){
			if(!$this->isCompleted()){
				$this->sql = rtrim($this->sql,"; \r\n\t\0\x0B") . ';';
			}
			return $this;
		}

		/**
		 * @return $this
		 */
		public function complete(){
			if(!$this->completed){
				$this->ending();
				$this->completed = true;
			}
			return $this;
		}

		/**
		 * @return $this
		 */
		public function reset(){
			$this->completed    = false;
			$this->sql          = '';
			$this->bindings     = [];
			$this->data_types   = [];
			return $this;
		}

		/**
		 * @param null $sql
		 * @param array|null $bindings
		 * @param null $dataTypes
		 * @return $this
		 */
		public function begin($sql,array $bindings = null, $dataTypes = null){
			return $this->reset()->push($sql,'',$bindings,$dataTypes);
		}

		/**
		 * @param $value
		 * @param $type
		 * @param null|string $name
		 * @param bool $asPrefix
		 * @return $this
		 */
		public function placeholder($value, $type = null, $name = null, $asPrefix = false){
			if($value instanceof RawValue){
				$this->sql.= $value->getValue();
			}else{
				$placeholderName = $this->placeholderName($name,$asPrefix);
				$this->sql.=$placeholderName;
				$this->bindings[$placeholderName] = $value;
				$this->data_types[$placeholderName] = $type?:Adapter::getBestDbColumnType($value);
			}
			return $this;
		}

		/**
		 * @param $text
		 * @return $this
		 */
		public function append($text){
			$this->sql.= $text;
			return $this;
		}

		/**
		 * @param $delimiter
		 * @param array $values
		 * @param null $types
		 * @param array|null $names
		 * @param bool|false $asPrefix
		 */
		public function placeholderList($delimiter, array $values, $types = null, array $names = null, $asPrefix = false){
			foreach($values as $i => $value){
				$raw = $value instanceof RawValue;
				if(!$raw && !is_scalar($value)){
					throw new \LogicException('Error placeholder value must be scalar value');
				}
				if($i > 0){
					$this->sql .= $delimiter;
				}
				if($raw){
					$type = null;
				}else{
					$type = isset($types[$i]) ? $types[$i] : null;
				}
				$name = isset($names[$i]) ? $names[$i] : null;
				$this->placeholder($value, $type, $name, $asPrefix);
			}
		}

		/**
		 * @param null $name
		 * @param bool|false $asPrefix
		 * @return string
		 */
		public function placeholderName($name = null, $asPrefix = false){
			if(is_array($name))$name = implode('_',$name);
			return ':'.($name && !$asPrefix?$name:System::uniqSysId(($name?:'ph').'_','dialect'));
		}

		/**
		 * @param $value
		 * @param $placeholder_prefix
		 * @param array $binds
		 * @param array $types
		 * @param null $type
		 * @return string
		 */
		public function valueCaptureToSql($value, $placeholder_prefix, &$binds = [ ], & $types = [ ], $type = null){
			if($value instanceof RawValue){
				return "$value";
			}else{
				$name = $this->placeholderName($placeholder_prefix,true);
				$binds[$name] = $value;
				$type[$name] = $type;
				return $name;
			}
		}


		/**
		 * @return bool
		 */
		public function isCompleted(){
			return $this->completed;
		}

		/**
		 * @return array
		 */
		public function getBindings(){
			return $this->bindings;
		}

		/**
		 * @return array
		 */
		public function getDataTypes(){
			return $this->data_types;
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return $this->sql;
		}

	}
}

