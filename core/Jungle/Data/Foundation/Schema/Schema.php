<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 18:53
 */
namespace Jungle\Data\Foundation\Schema {

	use Jungle\Data\Foundation\Schema\Indexed\IndexInterface;

	/**
	 * Class Schema
	 * @package Jungle\Data\Foundation\Schema
	 */
	abstract class Schema implements SchemaInterface, Indexed\SchemaInterface{

		/** @var  FieldInterface[] */
		protected $fields = [ ];

		/** @var  IndexInterface[] */
		protected $indexes = [ ];

		protected $_names;

		/**
		 * @param $name
		 * @return FieldInterface|null
		 */
		public function getField($name){
			foreach($this->fields as $field){
				if($field->getName() === $name){
					return $field;
				}
			}
			return null;
		}


		/**
		 * @param FieldInterface|string $field
		 * @return mixed
		 */
		public function getFieldIndex($field){
			return array_search($field, $this->fields, true);
		}

		/**
		 * @param $index
		 * @return FieldInterface
		 */
		public function getFieldByIndex($index){
			return $this->fields[$index];
		}

		protected function _initCache(){
			$names_fill = $this->_names===null;
			if($names_fill){
				$this->_names = [];
				foreach($this->fields as $field){
					$this->_names[] = $field->getName();
				}
			}
		}

		/**
		 * @return array
		 */
		public function getFieldNames(){
			$this->_initCache();
			return $this->_names;
		}



		/**
		 * @param FieldInterface $field
		 * @return $this
		 */
		public function addField(FieldInterface $field){
			if($this->beforeAddField($field)!==false){
				$name = $field->getName();
				foreach($this->fields as $f){
					if($f->getName() === $name){
						throw new \LogicException('Field "'.$name.'" already exists');
					}
				}
				$this->fields[] = $field;
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
		public function getPrimaryFieldName(){
			if(!$this->indexes){
				return $this->fields[0]->getName();
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
		 * @inheritDoc
		 */
		public function getPrimaryField(){
			if(!$this->indexes){
				return $this->fields[0];
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
			if(!$this->indexes){
				return $this->fields[0]->getName() === $field || $this->fields[0] === $field;
			}
			if($field instanceof FieldInterface) $field = $field->getName();
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

