<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.01.2016
 * Time: 4:02
 */
namespace Jungle\FileSystem {

	use Jungle\FileSystem;
	use Jungle\FileSystem\Model\Directory;
	use Jungle\FileSystem\Model\File;
	use Jungle\FileSystem\Model\IModel;
	use Jungle\FileSystem\Model\Permissions;

	/**
	 * Class OldModel
	 * @package Jungle\FileSystem
	 */
	abstract class OldModel implements IModel{

		/**
		 * @var string
		 */
		protected static $default_permissions = '0777';

		/**
		 * @var string
		 */
		protected $name;

		/**
		 * Directory for this OldModel
		 * @var Directory|null
		 */
		protected $parent;

		/**
		 * Existing source path to model
		 * @var string
		 */
		protected $source_path;

		/**
		 * @var bool
		 */
		protected $cloned = false;

		/**
		 * Will be deleted File or Directory
		 * @var  bool
		 */
		protected $deleted = false;

		/**
		 * @var bool
		 */
		protected $detached = false;


		/** @var array */
		protected $_dirty_properties = [];

		/** @var Permissions */
		protected $permissions;

		/** @var */
		protected $owner;

		/** @var */
		protected $group;




		protected $loading = false;

		/**
		 * @Constructor
		 * @param $name
		 * @param bool $nameIsExistingPath
		 */
		public function __construct($name, $nameIsExistingPath = false){
			$path = null;
			if($nameIsExistingPath){
				if(!file_exists($name)){
					throw new \LogicException($name . ' is not exists file');
				}
				$this->source_path      = realpath($name);
				$this->name             = basename($name);
			}else{
				$this->source_path  = null;
				$this->name         = $name;
			}
		}

		/**
		 * @String
		 * @return string
		 */
		public function __toString(){
			$this->apply();
			return $this->source_path;
		}


		/**
		 * Получить имя объекта
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * Выставить имя объекту
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			$o = $this->name;
			if($o!==$name && $this->_beforeNameChanged($o, $name)!==false){
				$this->name = $name;
				$this->_onNameChanged();
			}
			return $this;
		}

		protected function _onNameChanged(){}

		/**
		 * @param string $old
		 * @param string $name
		 * @return bool|void|null
		 */
		protected function _beforeNameChanged($old, $name){
			$p = $this->getParent();
			if(!$this->_isAvailableName($name,$p)){
				throw new \LogicException('Child "' . $name . '" already exists in inContainerDirectory "' . $p . '"');
			}

			if($this->source_path){
				$nPath = dirname($this->source_path) . DIRECTORY_SEPARATOR . $this->getName();
				if($this->_relocate($nPath)===false){
					return false;
				}
			}

			return true;
		}


		/**
		 * @param $name
		 * @param Directory|null $inContainerDirectory
		 * @return bool
		 */
		protected function _isAvailableName($name,Directory $inContainerDirectory = null){
			if(!$name){
				return true;
			}
			if($inContainerDirectory === null){
				$inContainerDirectory = $this->getParent();
			}
			return !($inContainerDirectory && $inContainerDirectory->getChild($name));
		}

		/**
		 * @param Directory $model
		 * @param bool $appliedInNew
		 * @param bool $appliedInOld
		 * @return $this
		 */
		public function setParent(Directory $model = null, $appliedInNew = false, $appliedInOld = false){
			$old = $this->parent;
			if($old !== $model && $this->_beforeParentChanged($model) !== false){
				$this->parent = $model;
				if(!$appliedInNew && $model){
					$model->addChild($this,true);
				}
				if(!$appliedInOld && $old){
					$old->removeChild($this,true);
				}
				$this->_onParentChanged($model, $old);
			}
			return $this;
		}

		/**
		 * @return Directory|null
		 */
		public function getParent(){
			if(!$this->parent && $this->source_path){
				$this->loading = true;
				$this->setParent(self::getExisting(dirname($this->source_path)));
				$this->loading = false;
			}
			return $this->parent;
		}

		/**
		 * @param Directory $directory
		 * @return bool|void
		 */
		protected function _beforeParentChanged(Directory $directory){

		}

		/**
		 * @param Directory|null $directory
		 * @param Directory|null $old
		 * @return bool|void
		 */
		protected function _onParentChanged(Directory $directory = null,Directory $old = null){

		}




		/** @return string */
		public function getSourcePath(){
			return $this->source_path;
		}

		/**
		 * @param null|int $o
		 * @return mixed
		 */
		protected function _getSourcePathInfo($o=null){
			return pathinfo($this->source_path,$o);
		}

		/**
		 * @return bool
		 */
		protected function _isSourceNameEqual(){
			return $this->isEqualName($this->_getSourcePathInfo(PATHINFO_BASENAME));
		}

		/**
		 *
		 */
		public function apply(){
			$this->_apply();
		}

		protected function _apply($applyAll = true){
			if(!$this->source_path){
				$parent = $this->getParent();
				if($parent){
					$sourcePath = $parent->getSourcePath();
					if(!$sourcePath){
						try{
							$parent->_apply(false);
							$sourcePath = $parent->getSourcePath();
						}catch(\LogicException $e){
							throw new \LogicException('Error in Apply top chains',0,$e);
						}
					}
				}else{
					throw new \LogicException('Apply "'.$this->getName().'" is not valid because existing parent is not Real source');
				}
				$this->_beforeCreate();
				$this->source_path = $sourcePath . DIRECTORY_SEPARATOR . $this->getName();
				$this->_create();
				$this->_afterCreate();
			}

			if($this->source_path && $this->cloned){
				$this->_applyCopy();
			}

			$this->_applyGroup();
			$this->_applyOwner();
			$this->_applyPermissions();


		}

		/**
		 * @return string
		 */
		protected function _getSourcePathToCreate(){
			$parent = $this->getParent();
			if($parent){
				$path = $parent->getSourcePath();
				return ($path? $path . DIRECTORY_SEPARATOR : '' ) . $this->getName();
			}else{
				throw new \LogicException('Parent not founded');
			}
		}

		/**
		 * @return void
		 * @throws
		 */
		abstract protected function _create();

		/**
		 * @return void
		 * @throws
		 */
		protected function _afterCreate(){
			$this->getPermissions();
		}

		protected function _beforeCreate(){

		}


		/**
		 * @return bool
		 */
		public function isPhantom(){
			return (bool)$this->source_path;
		}

		/** @return bool */
		public function isExists(){
			if($this->source_path){
				return file_exists($this->source_path);
			}
			return false;
		}

		/** @return bool */
		public function isWritable(){
			if($this->source_path){
				return is_writable($this->source_path);
			}return true;
		}

		/** @return bool */
		public function isReadable(){
			if($this->source_path){
				return is_readable($this->source_path);
			}return true;
		}

		/** @return bool */
		public function isExecutable(){
			if($this->source_path){
				return is_executable($this->source_path);
			} return true;
		}

		/** @return bool */
		public function isLink(){
			if($this->source_path){
				return is_link($this->source_path);
			} return true;
		}

		/** @return bool */
		public function isDeleted(){
			return $this->deleted;
		}

		/**
		 * @param $comparableName
		 * @return bool
		 */
		public function isEqualName($comparableName){
			return FileSystem::comparePathNames($comparableName, $this->getName() )===0;
		}

		/**
		 * @return $this
		 */
		public function delete(){
			if($this->source_path){

				if(!$this->deleted){
					$this->_delete();
					$this->deleted = true;
				}

			}
		}

		/**
		 * process delete
		 * @return void
		 */
		abstract protected function _delete();

		/**
		 * @param $path
		 * @return bool|Directory|File|IModel
		 */
		public static function getExisting($path){
			if(file_exists($path)){
				if(is_dir($path)){
					return new Directory($path,true);
				}else{
					return new File($path,true);
				}
			}else{
				throw new \LogicException(sprintf('path(%s) must be valid path to file',$path));
			}
		}


		/**
		 * @param $newPermissions
		 * @param bool|false $recursive
		 * @return $this
		 */
		public function setPermissions($newPermissions, $recursive = false){
			$this->getPermissions()->setPermissions($newPermissions);
			return $this;
		}

		/**
		 * @param null $octal if null will be return Permissions object
		 * @return Permissions|int|string
		 */
		public function getPermissions($octal = null){
			if($this->permissions === null){
				$this->permissions = (new FileSystem\Model\Permissions(null,function(){
						$this->_setPDirty('permissions');
						if($this->_applyPermissions()===false){
							$e = error_get_last();
							throw new \LogicException(sprintf('Error change permissions (Path: "%s", Message: "%s") ',$this->source_path, $e['message']));
						}
					}
				));
				$this->permissions->setPermissions(
					($this->source_path?$this->_getPermissions():$this->getDefaultPermissions())
					,false, true);
			}
			return $octal!==null? $this->permissions->getPermissions($octal): $this->permissions;
		}

		/**
		 * @return int
		 */
		protected function _getPermissions(){
			return fileperms($this->source_path);
		}


		/**
		 * @return string
		 */
		public static function getDefaultPermissions(){
			return static::$default_permissions;
		}

		/**
		 * @param $permissions
		 */
		public static function setDefaultPermissions($permissions){
			static::$default_permissions = $permissions;
		}

		/**
		 * @param $newOwner
		 * @return mixed
		 */
		public function setOwner($newOwner){
			$owner = $this->getOwner();
			if($owner !== $newOwner){
				$this->owner = $newOwner;
				$this->_setPDirty('owner');
				if($this->_applyOwner()===false){
					$e = error_get_last();
					throw new \LogicException(sprintf('Error change owner (Path: "%s", Message: "%s") ',$this->source_path, $e['message']));
				}
			}
		}

		/** @return mixed */
		public function getOwner(){
			if($this->owner === null && $this->source_path){
				$this->owner = $this->_getOwner();
			}
			return $this->owner;
		}

		/**
		 * @return int
		 */
		protected function _getOwner(){
			return fileowner($this->source_path);
		}

		/**
		 * @param $newGroup
		 * @return mixed
		 */
		public function setGroup($newGroup){
			$group = $this->getGroup();
			if($group !== $newGroup){
				$this->group = $newGroup;
				$this->_setPDirty('group');
				if($this->_applyGroup()===false){
					$e = error_get_last();
					throw new \LogicException(sprintf('Error change group (Path: "%s", Message: "%s") ',$this->source_path, $e['message']));
				}
			}
		}
		/** @return mixed */
		public function getGroup(){
			if($this->group === null && $this->source_path){
				$this->group = $this->_getGroup();
			}
			return $this->group;
		}

		/**
		 * @return int
		 */
		protected function _getGroup(){
			return filegroup($this->source_path);
		}




		/**
		 * @Apply group
		 * Применяет внутреннее свойство группы(если оно было изначально выставленно или получено)
		 * для реального источника данной модели
		 */
		protected function _applyGroup(){
			if($this->source_path && $this->group !== null && $this->_isPDirty('group')){
				if(!@chgrp($this->source_path,$this->group)){return false;}
				else{
					$this->group = $this->_getGroup();
					$this->_setPDirty('group',false);
				}
			}return true;
		}



		/**
		 * @Apply owner
		 * Применяет внутреннее свойство владельца(если оно было изначально выставленно или получено)
		 * для реального источника данной модели
		 */
		protected function _applyOwner(){
			if($this->source_path && $this->owner !== null && $this->_isPDirty('owner')){
				if(!@chown($this->source_path,$this->owner))return false;
				else{
					$this->owner = $this->_getOwner();
					$this->_setPDirty('owner',false);
				}
			} return true;
		}


		/**
		 * @Apply permissions
		 * Применяет внутреннее свойство прав доступа на объект(если оно было изначально выставленно или получено)
		 * для реального источника данной модели
		 */
		protected function _applyPermissions(){
			if($this->source_path && $this->permissions !== null && $this->_isPDirty('permissions')){
				if(!@chmod($this->source_path, $this->permissions->getMod(true))) return false;
				else{
					$this->permissions->setPermissions($this->_getPermissions(),false,true);
					$this->_setPDirty('permissions',false);
				}
			} return true;
		}




		/**
		 * @Relocate
		 * Изменить действительный source_path на переданый $path
		 * @param $path
		 * @return bool
		 * @TODO Необходимо продумать схему по которой будет меняться source_path вложеных объектов UPDATE
		 */
		protected function _relocate($path){
			if($this->source_path){
				if(!@rename($this->source_path, $path)){
					$e = error_get_last();
					throw new \LogicException();
				}
			}
			if(!file_exists($path)){
				throw new \LogicException('Relocate error: not found relocated');
			}
			$this->source_path = $path;
			return true;
		}

		public function __clone(){
			$this->cloned = true;
			if($this->permissions){
				$this->permissions = clone $this->permissions;
				$listener = $this->permissions->getOnChanged();
				if($listener){
					$listener = \Closure::bind($listener,$this);
					$this->permissions->setOnChanged($listener);
				}
				$this->_setPDirty('permissions');
			}
			$this->owner = $this->getOwner();$this->_setPDirty('owner');
			$this->group = $this->getGroup();$this->_setPDirty('group');
			$this->_afterClone();
		}

		protected function _afterClone(){}


		/**
		 *
		 */
		protected function _applyCopy(){
			$sourcePath = $this->getParent()->getSourcePath();
			$newSource = $sourcePath . DIRECTORY_SEPARATOR . $this->getName();
			$this->_copy($newSource);
			$this->source_path = $newSource;
			$this->cloned = false;
			$this->_setPDirty('permissions');
			$this->_setPDirty('owner');
			$this->_setPDirty('group');
			$this->_afterCopy();
		}

		protected function _afterCopy(){
			$this->_applyPermissions();
			$this->_applyGroup();
			$this->_applyOwner();
		}

		/**
		 * @param $destination
		 */
		protected function _copy($destination){
			if(!@copy($this->source_path, $destination)){
				$e = error_get_last();
				throw new \LogicException(sprintf('FileSystem Object(%s) could not be copied to "%s", message catched: "%s" ',$this->source_path,$destination,$e['message']));
			}
		}

		/**
		 * @param Directory $destination
		 */
		public function copyTo(Directory $destination){
			$clone = clone $this;
			$destination->addChild($clone);
			$clone->apply();
		}





		/**
		 * @param $property
		 * @return bool
		 */
		protected function _isPDirty($property){
			return isset($this->_dirty_properties[$property]);
		}

		/**
		 * @param $property
		 * @param bool|true $dirty
		 */
		protected function _setPDirty($property, $dirty = true){
			if($dirty){
				$this->_dirty_properties[$property] = true;
			}else{
				unset($this->_dirty_properties[$property]);
			}
		}

		protected function _clearPDirty(){
			$this->_dirty_properties = [];
		}




	}
}

