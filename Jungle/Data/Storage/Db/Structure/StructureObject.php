<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 03.03.2016
 * Time: 18:06
 */
namespace Jungle\Data\Storage\Db\Structure {

	use Jungle\Data\Storage\Db\Adapter;

	/**
	 * Class StructureObject
	 * @package Jungle\Data\Storage\Db\Structure
	 */
	abstract class StructureObject{

		/** @var  Adapter */
		protected $_adapter;

		/** @var bool */
		protected $_dirty       = false;

		/** @var bool  */
		protected $_new         = false;

		/** @var bool */
		protected $_removed     = false;

		/**
		 * @param Adapter $adapter
		 */
		public function __construct(Adapter $adapter){
			$this->_adapter = $adapter;
			$this->_new     = true;
		}

		/**
		 * @param bool|true $dirty
		 * @return $this
		 */
		public function setDirty($dirty = true){
			$this->_dirty = $dirty;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isDirty(){
			return $this->_dirty;
		}

		/**
		 * @param $new
		 * @return $this
		 */
		public function setNew($new){
			$new = boolval($new);
			if($this->_new !== $new){
				$this->_new = $new;
				if($new){
					$this->onNew();
				}
			}
			return $this;
		}

		public function onNew(){}


		/**
		 * @return bool
		 */
		public function isNew(){
			return $this->_new;
		}

		/**
		 * @return $this
		 */
		abstract public function save();

		/**
		 * @return $this
		 */
		abstract public function remove();

	}
}

