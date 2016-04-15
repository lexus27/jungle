<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.03.2016
 * Time: 0:54
 */
namespace Jungle\Data\DataMap\Collection {

	use Jungle\Data\DataMap\Schema;
	use Jungle\Event\Observable\ObservableTrait;
	use Jungle\Event\ObservableInterface;

	/**
	 * Class Source
	 * @package Jungle\Data\DataMap\Collection
	 *
	 * onInsert
	 * onRemoved
	 * onRead
	 */
	class Source implements ObservableInterface{

		use ObservableTrait;

		/**
		 * Конвертер данных для выдачи
		 * @var
		 */
		protected $converter;

		/**
		 * @var array
		 */
		protected $records = [];

		protected static function __define_events(){
			return [
				'beforeRead' => [
					'cancelable'        => true,
					'stoppable'         => true,
				],

				'read'

			];
		}

		/**
		 * @return mixed
		 */
		public function getConverter(){
			return $this->converter;
		}


		/**
		 * @param $criteria
		 * @param $offset
		 * @param $limit
		 * @return array [int, mixed[]]
		 */
		public function read($criteria=null, $offset=null, $limit=null){

			if($this->invokeEvent('beforeRead',[
				'criteria'  => $criteria,
				'offset'    => $offset,
				'limit'     => $limit
			])!==false){
				$this->invokeEvent('read',[
					'records' => $this->records,
					'count' => count($this->records)
				]);
			}
		}

		/**
		 * @param $criteria
		 * @return mixed
		 */
		protected function _criteriaExport($criteria){
			return $criteria;
		}

		/**
		 * @param $record
		 * @param Schema $schema
		 */
		public function insert($record, Schema $schema){

		}

		/**
		 * @param $record
		 * @param Schema $schema
		 */
		public function remove($record, Schema $schema){

		}

	}
}

