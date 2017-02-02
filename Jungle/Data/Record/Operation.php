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
	 * Class Operation
	 * @package Jungle\Data\Record
	 */
	class Operation implements Record\RecordAware{

		/** @var Record[] */
		public $operation_records = [ ];

		/** @var array */
		public $relation_points = [ ];

		/** @var array */
		public $parameters = [ ];


		/**
		 * @return bool
		 */
		public function isEmpty(){
			return empty($this->operation_records);
		}

		/**
		 * @param Record $record
		 * @return void
		 */
		public function startRecordCapture(Record $record){
			$this->operation_records[] = $record;
		}

		/**
		 * @param Record $record
		 * @return void
		 */
		public function endRecordCapture(Record $record){
			array_pop($this->operation_records);
		}

		/**
		 * @param $relation_key
		 * @return void
		 */
		public function startRelationCapture($relation_key){
			$this->relation_points[] = $relation_key;
		}

		/**
		 * @param $relation_key
		 * @return void
		 */
		public function endRelationCapture($relation_key){
			array_pop($this->relation_points);
		}

		/**
		 * @return null|string
		 */
		public function getElapsedPath(){
			return $this->relation_points ? implode('.', $this->relation_points) : null;
		}

		/**
		 * @return bool
		 */
		public function inRelationPath(){
			return count($this->operation_records) && count($this->relation_points);
		}

		/**
		 * @return Record|null
		 */
		public function getRecord(){
			return $this->operation_records ? $this->operation_records[0] : null;
		}

		/**
		 * @return Record|null
		 */
		public function getPrevRecord(){
			return count($this->operation_records)>1 ? array_slice($this->operation_records, -2, 1)[0] : null;
		}

		/**
		 * @return Record|null
		 */
		public function getCurrentRecord(){
			return $this->operation_records ? array_slice($this->operation_records,-1,1)[0] : null;
		}

		/**
		 * @return string
		 */
		public function getInitRelationName(){
			return isset($this->relation_points[0]) ? $this->relation_points[0] : null;
		}

		/**
		 * @return mixed
		 */
		public function getLastRelationName(){
			return $this->relation_points ? array_slice($this->relation_points, -1, 1)[0] : null;
		}


	}
}

