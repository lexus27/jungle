<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.06.2016
 * Time: 0:49
 */
namespace Jungle\Data\Record {
	
	use Jungle\Data\Record;
	use Jungle\Data\Record\Schema\Schema;
	use Jungle\Util\Data\ShipmentInterface;
	use Jungle\Util\Data\ShipmentOriginalInterface;

	/**
	 * Class RecordShipment
	 * @package Jungle\Data\Record\Schema
	 */
	class RecordShipment implements ShipmentInterface{

		/** @var  ShipmentOriginalInterface */
		protected $original_shipment;

		protected $original_names = [];

		/** @var  Schema */
		protected $schema;

		/**
		 * @param ShipmentOriginalInterface $shipment
		 * @return $this
		 */
		public function setOriginalShipment(ShipmentOriginalInterface $shipment){
			$this->original_shipment = $shipment;
			$this->original_names = [];
			return $this;
		}

		/**
		 * @return ShipmentOriginalInterface
		 */
		public function getOriginalShipment(){
			return $this->original_shipment;
		}

		/**
		 * @param Schema $schema
		 * @return $this
		 */
		public function setSchema(Schema $schema){
			$this->schema = $schema;
			return $this;
		}

		/**
		 * @return Schema
		 */
		public function getSchema(){
			return $this->schema;
		}

		/**
		 * @return int
		 */
		public function count(){
			return $this->original_shipment->count();
		}

		/**
		 * @return \Jungle\Data\Record|bool
		 */
		public function fetch(){
			$fetched = $this->original_shipment->fetch();
			if($fetched===false){
				return $fetched;
			}
			if($this->original_names===null){
				$this->original_names = $this->schema->getOriginalNames();
			}

			$data = [];
			foreach($this->original_names as $i => $name){
				if(array_key_exists($i, $fetched)){
					$data[$name] = $fetched[$i];
				}
			}unset($fetched);
			$record = $this->schema->makeRecord($data);
			return $record;
		}

		/**
		 * @return RecordShipment
		 */
		public function asIndexed(){
			return $this;
		}

		/**
		 * @return RecordShipment
		 */
		public function asAssoc(){
			return $this;
		}
	}
}

