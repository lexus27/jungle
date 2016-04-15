<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 19:21
 */
namespace Jungle\Data\DataMap {

	use Jungle\Data\DataMap\Collection\Source\Converter;
	use Jungle\Data\DataMap\Schema\Field;
	use Jungle\Data\DataMap\ValueAccess\Getter;
	use Jungle\Data\DataMap\ValueAccess\Setter;
	use Jungle\Data\DataMap\ValueAccess\ValueAccessAwareInterface;
	use Jungle\Data\Validator\ValidatorAwareInterface;
	use Jungle\Data\Validator\ValidatorInterface;
	use Jungle\Util\Value\Massive;

	/**
	 * Class Schema
	 */
	class Schema implements ValidatorAwareInterface, ValueAccessAwareInterface{

		/** @var  Schema\Field[]|null */
		protected $fields;

		/** @var  Schema\Index[] */
		protected $indexes = [];

		/** @var  callable|Converter|null */
		protected $data_converter;

		/** @var  ValidatorInterface|callable|null */
		protected $validator;

		/** @var  callable|Getter|null */
		protected $value_access_getter;

		/** @var callable|Setter|null */
		protected $value_access_setter;

		/**
		 * @param array|null $indexes
		 * @return $this
		 */
		public function setIndexes(array $indexes=[]){
			$this->indexes = $indexes;
			return $this;
		}

		/**
		 * @param Schema\Field[]|null $fields
		 * @param $original_order
		 * @return $this
		 */
		public function setFields(array $fields = null,$original_order = false){
			if($fields!==null){
				foreach($fields as $i => $field){
					if($original_order){
						$field->setOriginalKey($i);
					}
					$field->setSchema($this);
				}
			}
			$this->fields = $fields;
			return $this;
		}

		/**
		 * @return Schema\Index[]
		 */
		public function getIndexes(){
			return $this->indexes;
		}

		/**
		 * @return Schema\Field[]|null
		 */
		public function getFields(){
			return $this->fields;
		}

		/**
		 * @param callable|Converter|null $converter
		 * @return $this
		 */
		public function setDataConverter(callable $converter = null){
			$this->data_converter = Converter::checkoutConverter($converter);
			return $this;
		}

		/**
		 * @return callable|Converter|null
		 */
		public function getDataConverter(){
			return $this->data_converter;
		}

		/**
		 * @return Schema\Field
		 */
		public function getPrimary(){
			if($this->fields!==null){
				foreach($this->fields as $field){
					if($field->isPrimary()){
						return $field;
					}
				}
				return $this->getFieldByIndex(0);
			}else{
				return null;
			}
		}




		/**
		 * @param callable|Getter|null $getter
		 * @return $this
		 */
		public function setValueAccessGetter(callable $getter = null){
			$this->value_access_getter = $getter;
			return $this;
		}

		/**
		 * @return callable|Getter|null
		 */
		public function getValueAccessGetter(){
			return $this->value_access_getter;
		}

		/**
		 * @param callable|Setter|null $setter
		 * @return $this
		 */
		public function setValueAccessSetter(callable $setter = null){
			$this->value_access_setter = $setter;
			return $this;
		}

		/**
		 * @return callable|Setter|null
		 */
		public function getValueAccessSetter(){
			return $this->value_access_setter;
		}

		/**
		 * @return bool
		 */
		public function isOriginalActual(){
			return $this->fields === null;
		}

		/**
		 * @return callable|null
		 */
		public function isDataModified(){
			return $this->data_converter;
		}


		/**
		 * @param $name
		 * @return Schema\Field|null
		 */
		public function getField($name){
			return Massive::getNamed($this->fields,$name,'strcmp');
		}

		/**
		 * @param $index
		 * @return Field|null
		 */
		public function getFieldByIndex($index){
			if($this->fields!==null){
				return $this->fields[$index];
			}
			return null;
		}

		/**
		 * @param $name
		 * @return bool|int
		 */
		public function getFieldIndex($name){
			return Massive::searchNamed($this->fields,$name,'strcmp');
		}

		/**
		 * @param Field $field
		 * @return mixed
		 */
		public function searchField(Field $field){
			return array_search($field,(array)$this->fields,true);
		}



		/**
		 * @param $name
		 * @return string|null
		 */
		public function getFieldType($name){
			if(($field = $this->getField($name))){
				return $field->getType();
			}else{
				return null;
			}
		}



		/**
		 * @param callable|null $validator
		 * @return $this
		 */
		public function setValidator(callable $validator = null){
			$this->validator = $validator;
			return $this;
		}

		/**
		 * @return callable|ValidatorInterface|null
		 */
		public function getValidator(){
			return $this->validator;
		}

		/**
		 * @param $data
		 * @return bool
		 */
		public function isAllowedData($data){
			return true;
		}

		/**
		 * @param $data
		 * @return bool|array
		 */
		public function validate($data){
			if($this->validator){
				return call_user_func($this->validator,$data,'{data_object}');
			}
			return true;
		}


		/**
		 * @param $data
		 * @return mixed
		 */
		public function represent($data){
			if($this->data_converter){
				$data = call_user_func($this->data_converter,$data, $this);
			}
			return $data;
		}

		/**
		 * @param $field_name
		 * @return bool
		 */
		public function hasField($field_name){
			return Massive::searchNamed($this->fields,$field_name,'strcmp')!==false;
		}

		/**
		 * @param $data
		 * @param $property
		 * @param callable $default_getter
		 * @return mixed
		 */
		public function valueAccessGet($data, $property, callable $default_getter = null){
			if(!($getter = $this->getValueAccessGetter())){
				$getter = $default_getter?ValueAccess::checkoutGetter($default_getter):ValueAccess::getDefaultGetter();
			}
			if($this->isOriginalActual()){
				return call_user_func($getter,$data, $property);
			}else{
				if(($field = $this->getField($property))){
					return $field->callGetter($data,$getter);
				}else{
					throw new \LogicException('Access to not declared field "'.$property.'"!');
				}
			}
		}

		/**
		 * @param $data
		 * @param $property
		 * @param $value
		 * @param callable $default_setter
		 * @return mixed
		 */
		public function valueAccessSet($data, $property, $value, callable $default_setter = null){
			if(!($setter = $this->getValueAccessSetter())){
				$setter = $default_setter?ValueAccess::checkoutSetter($default_setter):ValueAccess::getDefaultSetter();
			}
			if($this->isOriginalActual()){
				$data = call_user_func($setter,$data,$property, $value);
			}else{
				if(($field = $this->getField($property))){
					$data = $field->callSetter($data,$value,$setter);
				}else{
					throw new \LogicException('Access to not declared field "'.$property.'"!');
				}
			}

			return $data;
		}

	}
}

