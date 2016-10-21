<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 18:54
 */
namespace Jungle\Util\Data\Schema\Indexed {

	use Jungle\Util\Named\NamedInterface;

	/**
	 * Class Index
	 * @package Jungle\Util\Data\Schema
	 */
	abstract class Index implements IndexInterface, NamedInterface{

		/** @var  string */
		protected $name;

		/** @var  string */
		protected $type;

		/** @var array */
		protected $fields = [];


		/**
		 * @param $field_name
		 * @param null $size
		 * @param null $direction
		 * @return $this
		 */
		public function addField($field_name, $size = null, $direction = null){
			$this->fields[$field_name] = [ $size, $direction ?: 'ASC' ];
			return $this;
		}

		/**
		 * @inheritDoc
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @inheritDoc
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param $type
		 * @return $this
		 */
		public function setType($type){
			$this->type = $type;
			return $this;
		}

		/**
		 * @inheritDoc
		 */
		public function getType(){
			return $this->type;
		}

		/**
		 * @inheritDoc
		 */
		public function getFieldNames(){
			return array_keys($this->fields);
		}

		/**
		 * @inheritDoc
		 */
		public function getFieldSize($field_name){
			return isset($this->fields[$field_name]) ? $this->fields[$field_name][1] : null;
		}

		/**
		 * @inheritDoc
		 */
		public function getFieldDirection($field_name){
			return isset($this->fields[$field_name]) ? $this->fields[$field_name][1] : null;
		}

		/**
		 * @inheritDoc
		 */
		public function hasField($field_name){
			return array_key_exists($field_name, $this->fields);
		}

	}
}

