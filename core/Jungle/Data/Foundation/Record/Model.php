<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 20:50
 */
namespace Jungle\Data\Foundation\Record {

	use Jungle\Data\Foundation\Record;
	use Jungle\Data\Foundation\Record\Collection\Relationship;
	use Jungle\Data\Foundation\Record\Head\Field;

	/**
	 * Class Model
	 * @package Jungle\Data\Bridge
	 */
	class Model extends Record{

		/** @var  array */
		protected $_initialized_properties = [];

		/** @var  array  */
		protected static $_auto_initialize_property_names = [];

		/**
		 *
		 */
		protected function onRecordReady(){
			foreach(static::$_auto_initialize_property_names as $property_name){
				$this->_getFrontProperty($property_name);
			}
		}


		public static function meta_define(){



		}

		/**
		 * @param null $fieldName
		 */
		protected function _resetAll($fieldName = null){
			if($fieldName === null){
				$this->_processed = [];
				$this->_initialized_properties = [];
			}else{
				unset($this->_processed[$fieldName]);
				unset($this->_initialized_properties[$fieldName]);
			}
			$this->onRecordReady();
		}

		/**
		 * @param null $fieldName
		 * @return mixed
		 */
		public function reset($fieldName = null){
			if($fieldName === null){
				$this->_initialized_properties = [];
			}else{
				unset($this->_initialized_properties[$fieldName]);
			}
			$this->onRecordReady();
		}

		/**
		 * @param $name
		 * @param $value
		 * @return mixed|void
		 */
		protected function _setFrontProperty($name, $value){
			$this->_initialized_properties[$name] = true;
			$this->{$name} = $value;
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		protected function _getFrontProperty($name){
			if(!isset($this->_initialized_properties[$name])){
				$this->_initialized_properties[$name] = true;
				return $this->{$name} = $this->_getProcessed($name);
			}
			return $this->{$name};
		}

	}
}

