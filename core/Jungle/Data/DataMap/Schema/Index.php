<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 21:01
 */
namespace Jungle\Data\DataMap\Schema {

	use Jungle\Data\DataMap\Collection;

	/**
	 * Class Index
	 * @package Jungle\Data\DataMap\Schema
	 */
	class Index{

		const TYPE_PRIMARY  = 1;

		const TYPE_UNIQUE   = 2;

		const TYPE_KEY      = 3;

		const TYPE_SPATIAL  = 4;



		protected $name;

		protected $fields       = [];

		protected $type         = self::TYPE_KEY;




		/**
		 * @param $field
		 * @return bool
		 */
		public function hasField($field){
			return in_array($field,array_column($this->fields,0),true);
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param int $type
		 * @return $this
		 */
		public function setType($type = self::TYPE_KEY){
			$this->type = $type;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getType(){
			return $this->type;
		}

		/**
		 * @param $field_name
		 * @param null $size
		 * @param null $direction
		 * @return $this
		 */
		public function setField($field_name, $size = null, $direction = null){
			$i = $this->searchField($field_name);
			if($i!==false){
				array_splice($this->fields,$i,1);
			}
			$this->fields[]     = [$field_name, $size, $direction];
			return $this;
		}

		/**
		 * @param $field_name
		 * @return $this
		 */
		public function removeField($field_name){
			$i = $this->searchField($field_name);
			if($i!==false){
				array_splice($this->fields,$i,1);
			}
			return $this;
		}

		/**
		 * @param $field_name
		 * @return bool|int
		 */
		public function searchField($field_name){
			return array_search($field_name,array_column($this->fields,0),true);
		}


		/**
		 * @return array = [
		 *      [FieldName],[Size|NULL],[Direction|NULL]
		 * ]
		 */
		public function getFields(){
			return $this->fields;
		}

		/**
		 * @return array
		 */
		public function getFieldNames(){
			return array_column($this->fields,0);
		}


		/**
		 * @param $data
		 * @param Collection $collection
		 * @return array
		 */
		public function getValuesFor($data,Collection $collection){
			return $collection->getFieldValues($data, $this->getFieldNames(), false);
		}


		/**
		 * @param $data
		 * @param $collection
		 * @return bool
		 */
		public function beforeAdded($data,Collection $collection){
			$field_names = array_column($this->fields,0);
			if($this->getType() === self::TYPE_UNIQUE){
				$values = $collection->getFieldValues($data,$field_names);
				foreach($collection as $item){
					$current_values[] = $collection->getFieldValues($item,$field_names);
					if($values === $current_values){
						return false;
					}
				}
			}
			return true;
		}

		public function beforeChanged($data,Collection $collection){

		}


	}
}

