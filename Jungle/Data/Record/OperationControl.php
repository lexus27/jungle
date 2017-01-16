<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 21.11.2016
 * Time: 15:54
 */
namespace Jungle\Data\Record {
	
	use Jungle\Data\Record;

	/**
	 * Class OperationControl
	 * @package Jungle\Data\Record
	 */
	class OperationControl{

		/** @var Record[]  */
		public $operation_records = [];

		/** @var array  */
		public $relations_levels = [];

		/** @var array  */
		public $parameters = [];

		/**
		 * @param Record $record
		 */
		public function start(Record $record){
			$this->operation_records[] = $record;
		}

		/**
		 * @param Record $record
		 * @return mixed
		 */
		public function end(Record $record){
			return array_pop($this->operation_records);
		}

		public function isEmpty(){
			return empty($this->operation_records);
		}


		/**
		 * @param $relation_key
		 */
		public function relationStart($relation_key){
			$this->relations_levels[] = $relation_key;
		}

		/**
		 * @param $relation_key
		 * @return mixed
		 */
		public function relationEnd($relation_key){
			return array_pop($this->relations_levels);
		}




		public function getCurrentRelationPath(){
			return $this->relations_levels?implode('.',$this->relations_levels):null;
		}
		/**
		 * @return Record
		 */
		public function getCurrentOperation(){
			end($this->operation_records);
			return current($this->operation_records);
		}

		/**
		 * @return Record
		 */
		public function getInitiator(){
			return array_slice($this->operation_records,-2,1)[0];
		}
		/**
		 * @return string
		 */
		public function getInitiativeAsRelation(){
			return array_slice($this->relations_levels,-1,1)[0];
		}

		public function isRelationOperation(){
			return count($this->operation_records) && count($this->relations_levels);
		}

	}
}

