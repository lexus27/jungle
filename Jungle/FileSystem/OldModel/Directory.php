<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.01.2016
 * Time: 4:03
 */
namespace Jungle\FileSystem\OldModel {

	use Jungle\FileSystem\OldModel;

	/**
	 * Class Directory
	 * @package Jungle\FileSystem\OldModel
	 */
	class Directory extends OldModel{

		/** @var string */
		protected static $default_permissions = '555';

		/** @var OldModel[]  */
		protected $children = [];

		/**
		 *
		 */
		protected function _onNameChanged(){
			foreach($this->children as $child){
				$child->source_path = $this->source_path . DIRECTORY_SEPARATOR . $child->getName();
			}
		}

		/**
		 * @param bool $applyAll
		 */
		protected function _apply($applyAll = true){
			parent::_apply($applyAll);
			if($applyAll){
				foreach($this->children as $child){
					if($child->isDeleted()){
						$this->removeChild($child);
					}else{
						$child->_apply($applyAll);
					}
				}
			}
		}

		/**
		 * Create directory
		 */
		protected function _create(){
			if(!@mkdir($this->source_path,0777)){
				$e = error_get_last();
				throw new \LogicException(sprintf('Error create directory "%s" , message: %s',$this->source_path,$e['message']));
			}
		}

		/**
		 * After clone process
		 */
		protected function _afterClone(){
			$this->_loadChildren();
			foreach($this->children as  & $child){
				$child = clone $child;
			}
		}

		/**
		 * @param $destination
		 */
		protected function _copy($destination){
			if(!@mkdir($this->source_path, $destination)){
				$e = error_get_last();
				throw new \LogicException(sprintf('FileSystem Object(%s) could not be copied to "%s", message catched: "%s" ',$this->source_path,$destination,$e['message']));
			}
		}

		/**
		 * Load all not loaded children objects
		 */
		protected function _loadChildren(){
			if($this->source_path){
				$children = glob($this->source_path . DIRECTORY_SEPARATOR . '*');
				foreach($children as $path){
					$name = basename($path);
					if(!$this->getChild($name)){
						$this->addChild( $this->_loadExistingChild($name) );
					}
				}
			}
		}

		/**
		 * @param OldModel $model
		 * @param bool $appliedParentInModel
		 * @return $this
		 */
		public function addChild(OldModel $model, $appliedParentInModel = false){
			if( $this->searchChild($model) === false && $this->_beforeChildAdded($model) !== false){
				$this->children[] = $model;
				if(!$appliedParentInModel){
					$model->setParent($this,true);
				}
				$this->_onChildAdded($model);
			}
			return $this;
		}

		/**
		 * @param OldModel $model
		 * @return bool|int
		 */
		public function searchChild(OldModel $model){
			return array_search($model,$this->children,true);
		}

		/**
		 * @param OldModel $model
		 * @param bool $appliedDetachFromParent
		 * @return $this
		 */
		public function removeChild(OldModel $model,$appliedDetachFromParent = false){
			if(($i = $this->searchChild($model))!==false && $this->_beforeChildRemoved($model)!==false){
				array_splice($this->children,$i,1);
				if(!$appliedDetachFromParent){
					$model->setParent(null,true,true);
				}
				$this->_onChildRemoved($model);
			}
			return $this;
		}



		/**
		 * @param $name
		 * @return IModel|Directory|File|null
		 */
		public function getChild($name){
			if($name instanceof IModel)$name = $name->getName();
			if(($child = $this->_getLoadedChild($name))){
				return $child;
			}
			if($this->_hasExistingChild($name) && ($child = $this->_loadExistingChild($name))){
				$this->loading = true;
				$child->setParent($this);
				$this->loading = false;
				return $child;
			}
			return null;
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function hasChild($name){
			if($name instanceof IModel)$name = $name->getName();
			return $this->_getLoadedChild($name) || $this->_hasExistingChild($name);
		}

		/**
		 * @param $name
		 * @return File|null
		 */
		public function getFile($name){
			if($name instanceof IModel)$name = $name->getName();
			if(($child = $this->_getLoadedChild($name)) && $child instanceof File){
				return $child;
			}
			if($this->_isExistingChildFile($name) && ($child = $this->_loadExistingChild($name))){
				$child->setParent($this);
				return $child;
			}
			return null;
		}

		/**
		 * @param $name
		 * @return Directory|null
		 */
		public function getDirectory($name){
			if($name instanceof IModel)$name = $name->getName();
			if(($child = $this->_getLoadedChild($name)) && $child instanceof Directory){
				return $child;
			}
			if($this->_isExistingChildDirectory($name) && ($child = $this->_loadExistingChild($name))){
				$child->setParent($this);
				return $child;
			}
			return null;
		}

		/**
		 * @param $name
		 * @return IModel|null
		 */
		protected function _getLoadedChild($name){
			foreach($this->children as $child){
				if($child->isEqualName($name)){
					return $child;
				}
			}
			return null;
		}



		/**
		 * @param $name
		 * @return bool
		 */
		protected function _hasExistingChild($name){
			return file_exists($this->source_path . DIRECTORY_SEPARATOR . $name);
		}

		/**
		 * @param $name
		 * @return IModel|Directory|null
		 */
		protected function _loadExistingChild($name){
			return OldModel::getExisting($this->source_path . DIRECTORY_SEPARATOR . $name);
		}

		/**
		 * @param $name
		 * @return bool
		 */
		protected function _isExistingChildFile($name){
			return is_file($this->source_path . DIRECTORY_SEPARATOR . $name);
		}

		/**
		 * @param $name
		 * @return bool
		 */
		protected function _isExistingChildDirectory($name){
			return is_dir($this->source_path . DIRECTORY_SEPARATOR . $name);
		}





		/**
		 * @return \Generator|OldModel[]
		 */
		public function getChildren(){
			foreach($this->_getChildrenNames() as $name){
				$child = $this->getChild($name);
				if($child){
					yield $child;
				}
			}
		}

		protected function _getChildrenNames(){
			$n = [];
			foreach($this->children as $c){
				$n[] = $c->getName();
			}
			foreach(glob($this->source_path . DIRECTORY_SEPARATOR . '*') as $path){
				$name = basename($path);
				if(!in_array($name,$n,true)){
					$n[] = $name;
				}
			}
			return $n;
		}

		/**
		 * process delete
		 * @return void
		 */
		protected function _delete(){

		}



		/**
		 * @param OldModel $child
		 * @return bool|void
		 */
		protected function _beforeChildAdded(OldModel $child){
			if(!$this->loading && !$child->loading && $this->hasChild($child->getName())){
				throw new \LogicException(sprintf('Child with name %s already exists in directory "%s" , named: %s',$child->getName(), $this->source_path , $this->getName()));
			}
			return true;
		}

		/**
		 * @param OldModel $child
		 * @return void
		 */
		protected function _onChildAdded(OldModel $child){

		}

		/**
		 * @param OldModel $child
		 * @return bool|void
		 */
		protected function _beforeChildRemoved(OldModel $child){

			return true;
		}
		/**
		 * @param OldModel $child
		 * @return void
		 */
		protected function _onChildRemoved(OldModel $child){

		}


	}
}

