<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 18:53
 */
namespace Jungle\Util\Data\Schema {

	use Jungle\Util\Data\Schema\Indexed\IndexInterface;

	/**
	 * Class Schema
	 * @package Jungle\Util\Data\Schema
	 */
	abstract class Schema implements SchemaInterface, Indexed\SchemaInterface{

		/** @var  FieldInterface[] */
		protected $fields = [ ];

		/** @var  array  */
		protected $field_indexes = [];

		/** @var  IndexInterface[] */
		protected $indexes = [ ];

		/**
		 * @param $name
		 * @return FieldInterface|null
		 */
		public function getField($name){
			if(isset($this->field_indexes[$name])){
				return $this->fields[$this->field_indexes[$name]];
			}
			return null;
		}


		/**
		 * @param FieldInterface|string $field
		 * @return mixed
		 */
		public function getFieldIndex($field){
			if(is_string($field)){
				return isset($this->field_indexes[$field])?$this->field_indexes[$field]:false;
			}else{
				return array_search($field, $this->fields, true);
			}
		}

		/**
		 * @param $index
		 * @return FieldInterface
		 */
		public function getFieldByIndex($index){
			return isset($this->fields[$index])?$this->fields[$index]:null;
		}

		protected function _initCache(){

		}

		/**
		 * @return array
		 */
		public function getFieldNames(){
			return array_keys($this->field_indexes);
		}



		/**
		 * @param FieldInterface $field
		 * @return $this
		 */
		public function addField(FieldInterface $field){
			if(!$field->getName()){
				throw new \LogicException('Field passed without name');
			}
			if($this->beforeAddField($field)!==false){
				$name = $field->getName();
				foreach($this->fields as $f){
					if($f->getName() === $name){
						throw new \LogicException('Field "'.$name.'" already exists');
					}
				}
				$c = count($this->fields);
				$this->fields[$c] = $field;
				$this->field_indexes[$name] = $c;
				$field->setSchema($this);
				$this->afterAddField($field);
			}
			return $this;
		}

		/**
		 * @param $field
		 */
		protected function beforeAddField($field){}

		protected function afterAddField($field){}

		/**
		 * @inheritDoc
		 */
		public function getFields(){
			return $this->fields;
		}


		/**
		 * @return string
		 */
		public function getPk(){
			if(!$this->indexes){
				/** @var FieldInterface[] $f */
				$f = array_slice($this->fields,0,1,false);
				return $f[0]->getName();
			}
			foreach($this->fields as $field){
				foreach($this->indexes as $index){
					$name = $field->getName();
					if($index->hasField($name) && $index->getType() === $index::TYPE_PRIMARY){
						return $name;
					}
				}
			}
			return null;
		}

		/**
		 * @return FieldInterface
		 */
		public function getFirstField(){
			return $this->fields[0];
		}

		/**
		 * @inheritDoc
		 */
		public function getPkField(){
			if(!$this->indexes){
				return $this->getFirstField();
			}
			foreach($this->fields as $field){
				foreach($this->indexes as $index){
					if($index->hasField($field->getName()) && $index->getType() === $index::TYPE_PRIMARY){
						return $field;
					}
				}
			}
			return null;
		}

		/**
		 * @param FieldInterface|string $field
		 * @return bool
		 */
		public function isPrimaryField($field){
			if($field instanceof FieldInterface){
				$field = $field->getName();
			}
			if(!$this->indexes){
				return $this->getFirstField()->getName() === $field;
			}
			foreach($this->indexes as $index){
				if($index->hasField($field) && $index->getType() === $index::TYPE_PRIMARY){
					return true;
				}
			}
			return false;
		}

		/**
		 * @param FieldInterface|string $field
		 * @return bool
		 */
		public function isUniqueField($field){
			if($this->isPrimaryField($field)){
				return true;
			}
			if($field instanceof FieldInterface) $field = $field->getName();
			foreach($this->indexes as $index){
				if($index->hasField($field) && $index->getType() === $index::TYPE_UNIQUE){
					return true;
				}
			}
			return false;
		}

		/**
		 * @param IndexInterface $index
		 * @return $this
		 */
		public function addIndex(IndexInterface $index){
			$name = $index->getName();
			foreach($this->indexes as $i){
				if($i->getName() === $name){
					throw new \LogicException('Index name "'.$name.'" already exists in schema!');
				}
			}
			$this->indexes[] = $index;
			return $this;
		}

		/**
		 * @param $name
		 * @return IndexInterface|null
		 */
		public function getIndex($name){
			foreach($this->indexes as $index){
				if($index->getName() === $name){
					return $index;
				}
			}
			return null;
		}

		/**
		 * @return IndexInterface[]
		 */
		public function getIndexes(){
			return $this->indexes;
		}


	}
}

