<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 18:53
 */
namespace Jungle\Util\Data\Foundation\Schema {

	/**
	 * Class Field
	 * @package Jungle\Util\Data\Foundation\Schema
	 */
	abstract class Field implements FieldInterface, Indexed\FieldInterface{

		/** @var  Schema */
		protected $schema;

		/** @var  string */
		protected $name;

		/** @var  string */
		protected $type = 'string';

		/** @var  null */
		protected $default = null;

		/** @var  bool */
		protected $nullable = false;

		/**
		 * Field constructor.
		 * @param $name
		 * @param string $type
		 */
		public function __construct($name, $type = null){
			$this->name = $name;
			$this->type = $type?:'string';
		}

		/**
		 * @param Schema $schema
		 * @return $this
		 */
		public function setSchema(Schema $schema){
			if($this->schema !== $schema){
				$this->schema = $schema;
			}
			return $this;
		}

		/**
		 * @return Schema
		 */
		public function getSchema(){
			return $this->schema;
		}


		/**
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @return string
		 */
		public function getType(){
			return $this->type;
		}

		/**
		 * @return mixed
		 */
		public function getDefault(){
			return $this->default;
		}

		/**
		 * @return bool
		 */
		public function isNullable(){
			return $this->nullable;
		}

		/**
		 * @return bool
		 */
		public function isDefaultNull(){
			return $this->default === null && $this->nullable;
		}


		/**
		 * @return bool
		 */
		public function isPrimary(){
			return $this->schema->isPrimaryField($this->name);
		}

		/**
		 * @return bool
		 */
		public function isUnique(){
			return $this->schema->isPrimaryField($this->name);
		}

	}
}

