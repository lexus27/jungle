<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 0:52
 */
namespace Jungle\Storage\Db {

	use Jungle\Storage\Db\Structure\Column;

	/**
	 * Class Sql
	 * @package Jungle\Storage\Db
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
		public function push($sql, $delimiter = null, array $bindings = null,$dataTypes = null){

			if($this->isCompleted()){
				throw new \LogicException('Sql is completed!');
			}

			if(!$delimiter)$delimiter = $this->default_delimiter;
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
					$this->bindings[is_int($index)?count($this->bindings):$index] = $bind;
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
					$this->data_types[is_int($index)?count($this->data_types):$index] = $type;
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
		 * @param null $delimiter
		 * @param array|null $bindings
		 * @param null $dataTypes
		 * @return $this
		 */
		public function begin($sql, $delimiter = null,array $bindings = null, $dataTypes = null){
			return $this->reset()->push($sql,$delimiter,$bindings,$dataTypes);
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
			$dataTypes = $this->data_types;
			foreach($dataTypes as & $type){
				if(is_null($type)){
					$type = $this->default_data_type;
				}
			}
			return $dataTypes;
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return $this->sql;
		}

	}
}

