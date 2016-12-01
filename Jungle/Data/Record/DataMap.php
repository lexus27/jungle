<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 20:50
 */
namespace Jungle\Data\Record {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Schema\Schema;

	/**
	 * Class DataMap - Предвестник ORM ~ Моделей
	 * @package modelX
	 */
	class DataMap extends Record{

		/** @var array */
		protected $_processed = [];

		/** @var array  */
		protected $_properties = [];

		/**
		 * DataMap constructor.
		 * @param Schema $schema
		 * @param null $data
		 */
		public function __construct(Schema $schema, $data = null){
			parent::__construct();
			$this->setSchema($schema);
			if($data!==null){
				$this->setRecordState(self::STATE_LOADED, $data);
			}else{
				$this->setRecordState(self::STATE_NEW, $data);
			}
			$this->onConstruct();
		}

		/**
		 * @param null $fieldName
		 * @return mixed
		 */
		public function reset($fieldName = null){
			if($fieldName === null){
				$this->_properties = [];
				$this->_afterReset();
			}else{
				unset($this->_properties[$fieldName]);
			}
			$this->onRecordReady();
		}

		/**
		 * @param null $fieldName
		 */
		protected function _resetAll($fieldName = null){
			if($fieldName === null){
				$this->_processed = [];
				$this->_properties = [];
				$this->_afterResetAll();
			}else{
				unset($this->_processed[$fieldName]);
				unset($this->_properties[$fieldName]);
			}
			$this->onRecordReady();
		}

		/**
		 * @param $name
		 * @param $value
		 * @return mixed
		 */
		protected function _setFrontProperty($name, $value){
			$this->_properties[$name] = $value;
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		protected function &_getFrontProperty($name){
			if(!array_key_exists($name, $this->_properties)){
				$this->_properties[$name] = $this->_getProcessed($name);
			}
			return $this->_properties[$name];
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function isInitializedProperty($name){
			return array_key_exists($name,$this->_properties);
		}


		/**
		 * @return bool
		 */
		protected function _doCreate(){
			if(parent::_doCreate()){
				$this->_processed = $this->_properties;
				return true;
			}
			return false;
		}

		/**
		 * @param $dirty_data
		 * @return bool
		 */
		protected function _doUpdate($dirty_data){
			if(parent::_doUpdate($dirty_data)){
				$this->_processed = $this->_properties;
				return true;
			}
			return false;
		}

	}
}

