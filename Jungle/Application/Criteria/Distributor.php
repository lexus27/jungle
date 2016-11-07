<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.11.2016
 * Time: 16:15
 */
namespace Jungle\Application\Criteria {

	use Jungle\Data\Record\Collection;

	/**
	 * Class Distributor
	 * @package Jungle\Application\Criteria
	 */
	class Distributor implements \JsonSerializable, \IteratorAggregate{

		/** @var  Criteria */
		protected $criteria;

		/** @var  TransceiverInterface */
		protected $transceiver;

		protected $scope;

		protected $collection;

		protected $count;

		protected $total;


		/**
		 * Distributor constructor.
		 * @param $scope
		 * @param Collection $collection
		 * @param TransceiverInterface $transceiver
		 */
		public function __construct($scope, Collection $collection, TransceiverInterface $transceiver){
			$this->scope        = $scope;
			$this->collection   = $collection->extend();
			$this->transceiver  = $transceiver;
		}

		/**
		 * @return mixed
		 */
		public function getCount(){
			if(!$this->criteria){
				$this->apply();
			}
			return $this->count;
		}

		/**
		 * @return mixed
		 */
		public function getTotalCount(){
			if(!$this->criteria){
				$this->apply();
			}
			return $this->total;
		}

		/**
		 * @return Collection
		 */
		public function getCollection(){
			if(!$this->criteria){
				$this->apply();
			}
			return $this->collection;
		}

		public function getIterator(){
			if(!$this->criteria){
				$this->apply();
			}
			return $this->collection;
		}

		/**
		 * @param Criteria $criteria
		 * @return $this
		 */
		public function apply(Criteria $criteria = null){

			$criteria = ($criteria?: $this->createCriteria());
			$this->criteria = $criteria;

			$this->before();

			$collection = $this->collection;
			$this->transceiver->receiveTo($this->scope, $criteria);

			isset($criteria->condition) && $collection->setContainCondition($criteria->condition);

			$this->counting();

			isset($criteria->order)     && $collection->setSorter($criteria->order);
			isset($criteria->offset)    && $collection->setOffset($criteria->offset);
			isset($criteria->limit)     && $collection->setLimit($criteria->limit);

			$this->after();
			return $this;
		}

		protected function before(){}

		protected function after(){}


		/**
		 * @return Scroller
		 */
		public function getScroller(){

			if(!$this->criteria){
				$this->apply();
			}

			return new Scroller(
				$this->total,
				$this->criteria->limit,
				$this->criteria->offset,
				function($index, Scroller $scroller){
					return $this->transceiver->linkPage($this->scope, $index, $scroller);
				}
			);
		}


		protected function counting(){
			$criteria = $this->criteria;
			$this->total = $this->fetchCount();
			$subsequent = $this->total - $criteria->offset;
			$this->count = $subsequent > $criteria->limit? $criteria->limit: $subsequent;
		}


		/**
		 * @param Collection $collection
		 * @return int
		 */
		protected function fetchCount(Collection $collection = null){
			$collection = $collection?: $this->collection;
			$collection->setSyncLevel($collection::SYNC_STORE);
			try{
				return $collection->count();
			}finally{
				$collection->setSyncLevel($collection::SYNC_LOCAL);
			}
		}

		/**
		 * @return Criteria
		 */
		protected function createCriteria(){
			return new Criteria();
		}


		/**
		 * @return array
		 */
		public function jsonSerialize(){
			return [
				'total_count' => $this->total,
				'count' => $this->count,
				'items' => $this->collection
			];
		}


	}
}

