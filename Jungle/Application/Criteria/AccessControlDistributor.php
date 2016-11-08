<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.11.2016
 * Time: 19:59
 */
namespace Jungle\Application\Criteria {

	use Jungle\Application\Dispatcher;
	use Jungle\Data\Record\Collection;
	use Jungle\User\AccessControl\Context\ObjectAccessor;
	use Jungle\User\AccessControl\Manager;

	/**
	 * Class AccessControlDistributor
	 * @package Jungle\Application\Criteria
	 */
	class AccessControlDistributor extends Distributor{

		const ITERATE_ANY = 'any';
		const ITERATE_ALLOWED = 'allowed';
		const ITERATE_DENY = 'deny';


		/** @var Dispatcher  */
		protected $dispatcher;

		/** @var Manager */
		protected $manager;

		/** @var  Collection */
		protected $collection;

		/** @var  Collection */
		protected $allowed_collection;

		/** @var  bool */
		protected $capture_denied = false;

		/** @var string  */
		protected $iterate_type = self::ITERATE_ALLOWED;


		/**
		 * AccessControlDistributor constructor.
		 * @param $scope
		 * @param Collection $collection
		 * @param TransceiverInterface $transceiver
		 * @param Dispatcher $dispatcher
		 * @param Manager $manager
		 */
		public function __construct($scope, Collection $collection, TransceiverInterface $transceiver, Dispatcher $dispatcher, Manager $manager){
			$this->scope = $scope;
			$this->collection = $collection->extend();
			$this->transceiver = $transceiver;
			$this->dispatcher = $dispatcher;
			$this->manager = $manager;
		}


		/**
		 *
		 */
		public function before(){
			$this->allowed_collection = null;
			$objectAccessor = new ObjectAccessor([
				'class' => $this->collection->getSchema()->getName(),
				'predicate_effect' => true,
			]);
			$result = $this->manager->enforce('read_collection',$objectAccessor,true);
			if($result->isAllowed() === $objectAccessor->getPredicateEffect()){
				$condition = $objectAccessor->getSelectConditions();
				$this->allowed_collection = $this->collection->extend($condition);
			}
		}

		/**
		 * @return \ArrayIterator|Collection
		 */
		public function getCollection(){
			if($this->capture_denied){
				return $this->collection;
			}elseif($this->allowed_collection){
				return $this->allowed_collection;
			}else{
				return new \ArrayIterator([]);
			}
		}

		public function setIterateType($iterateType = self::ITERATE_ALLOWED){
			$this->iterate_type = $iterateType;
			return $this;
		}

		/**
		 * @return \ArrayIterator|Collection
		 */
		public function getIterator(){
			if($this->iterate_type === self::ITERATE_ANY){
				return $this->collection;
			}elseif($this->iterate_type === self::ITERATE_ALLOWED){
				return $this->allowed_collection?: $this->collection;
			}else{
				if($this->capture_denied){
					return new \ArrayIterator($this->collectDenied());
				}else{
					return new \ArrayIterator([]);
				}
			}
		}

		/**
		 * @return array
		 */
		public function collectAllowed(){
			return $this->allowed_collection?$this->allowed_collection->getItems():[];
		}

		/**
		 * @return array
		 */
		public function collectDenied(){
			$a = [];
			foreach($this->collection as $item){
				if(!$this->allowed_collection->has($item->getIdentifierValue())){
					$a[] = $item;
				}
			}
			return $a;
		}

		/**
		 *
		 */
		protected function counting(){
			if($this->capture_denied){
				$this->total = $this->fetchCount($this->collection);
			}else{
				if($this->allowed_collection){
					$criteria = $this->criteria;
					$this->total = $this->fetchCount($this->allowed_collection);
					$subsequent = $this->total - $criteria->offset;
					$this->count = $subsequent > $criteria->limit? $criteria->limit: $subsequent;
				}else{
					$this->total = 0;
					$this->count = 0;
				}
			}
		}



	}
}

