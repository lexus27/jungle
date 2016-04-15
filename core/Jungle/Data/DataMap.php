<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 18:57
 */
namespace Jungle\Data {

	use Jungle\Data\DataMap\Collection;
	use Jungle\Data\DataMap\Schema;
	use Jungle\Data\DataMap\ValueAccess;
	use Jungle\Data\DataMap\ValueAccess\ValueAccessAwareInterface;

	/**
	 * Class DataMap
	 * @package Jungle\Data
	 */
	class DataMap implements ValueAccessAwareInterface{

		/** @var  \Jungle\Data\DataMap\Collection */
		protected $_collection;

		/** @var  Schema */
		protected $_schema;

		/** @var  mixed */
		protected $_stored_data;

		/**
		 * @param callable|\Jungle\Data\DataMap\Criteria\CriteriaInterface $criteria
		 * @return bool
		 */
		public function match(callable $criteria){
			return boolval(call_user_func($criteria,$this->_stored_data,$this));
		}

		/**
		 * @param array $values
		 * @param array $white_list
		 */
		public function assign($values, array $white_list = null){

			foreach($values as $key => $value){

				if($white_list!==null && !in_array($key,$white_list,true)){
					continue;
				}

				if($this->_schema->hasField($key)){
					$this->_stored_data = $this->valueAccessSet($this->_stored_data,$key,$value);
				}
			}

			foreach($this->_schema->getFields() as $field){
				$field_name = $field->getName();
				$value = isset($values[$field_name])?$values[$field_name]:null;

			}

		}

		/**
		 * @param $data
		 * @param $property
		 * @return mixed
		 */
		public function valueAccessGet($data, $property){
			return $this->_schema->valueAccessGet($data,$property);
		}

		/**
		 * @param $data
		 * @param $property
		 * @param $value
		 * @return mixed
		 */
		public function valueAccessSet($data, $property, $value){
			return $this->_schema->valueAccessSet($data,$property,$value);
		}


	}

}

