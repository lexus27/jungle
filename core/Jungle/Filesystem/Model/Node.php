<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 02.02.2016
 * Time: 21:33
 */
namespace Jungle\FileSystem\Model {

	use Jungle\FileSystem\Model\Exception\ActionError;
	use Jungle\FileSystem\Model\Exception\AlreadyExistsIn;
	use Jungle\FileSystem\Model\Exception\ProcessLock;
	use Jungle\FileSystem\Model\Manager\Adapter;

	/**
	 * Class Node
	 * @package Jungle\FileSystem\Model
	 * @property Adapter $adapter
	 * @property Manager $manager
	 * @property Directory $parent
	 * @property string $basename
	 * @property string $path
	 *
	 * @property Permissions $permissions
	 * @property int $owner
	 * @property int $group
	 *
	 * @property int $access_time
	 * @property int $modify_time
	 * @property int $created_time
	 */
	abstract class Node{

		/** @var Manager */
		protected $manager;

		/** @var  Directory */
		protected $parent;

		/** @var  string */
		protected $real_path;

		/** @var  string */
		protected $basename;

		/**
		 * @var  bool @State
		 * может быть FALSE по нескольким причинам:
		 * Если объект был клонирован для копирования , источник остается в виде пути, дополнительно помечается @see cloning
		 * Если объект является подготавливаемым к созданию,
		 * Если объект был удален,
		 */
		protected $exists;

		/**
		 * @var  bool @State
		 * Зависимости @see deleted = TRUE:
		 * @see exists      = false;
		 * @see real_path   = null;
		 */
		protected $deleted;

		/**
		 * @var  bool @State
		 * Зависимости @see cloning TRUE:
		 * @see exists = false;
		 * @see real_path Остается прежним пока не произойдет операция копирования (cloning = false, exists = true)
		 */
		protected $cloning;

		/**
		 * @var  bool @State
		 * Объект был отсоеденен от родителя, в данном случае он является фантомным(висячим)
		 * Зависимости @see exists = true, Объект по прежнему существует,
		 * доступ к источнику остается, но при подключении такой иерархии к
		 * актуальной директории, он автоматически переносится
		 */
		protected $moving;



		/** @var  Permissions @stats */
		protected $permissions = null;

		/** @var  int @stats */
		protected $owner;

		/** @var  int @stats */
		protected $group;


		/**
		 * @param $name
		 * @param $nameIsReal
		 * @param Manager $manager
		 * @throws Exception
		 */
		public function __construct($name,$nameIsReal, Manager $manager){
			$this->manager = $manager;
			$this->manager->regNode($this);
			if($nameIsReal){
				if(!$this->checkExistingNodeType($name)){
					throw new Exception('Node type from absolute "'.$name.'" is not supported '.get_called_class());
				}
				$baseName           = basename($name);
				$this->setBasename($baseName);
				$this->real_path    = ltrim(dirname($name),'.\\/').$manager->getAdapter()->ds().$baseName;
				$this->exists       = true;
			}else{
				$this->setBasename(basename($name));
				$this->real_path    = null;
				$this->exists       = false;
			}
			$this->cloning   = false;
			$this->moving   = false;
			$this->deleted  = false;
		}

		/**
		 * @return bool
		 */
		abstract public function isEmpty();

		/**
		 * @return float
		 */
		abstract public function getSize();

		/**
		 * @param bool|false $recursive
		 * @return $this
		 */
		abstract public function setReadOnly($recursive = false);

		/**
		 * @return bool
		 * @throws Exception
		 */
		public function isWritable(){
			if($this->real_path) return $this->getAdapter()->is_writable($this->real_path);
			else return true;
		}

		/**
		 * @return bool
		 */
		public function isDeletable(){
			return $this->isReadOnly() || !$this->isWritable();
		}

		/**
		 * @return bool
		 * @throws Exception
		 */
		public function isReadOnly(){
			return $this->getPermissions()->getMod(true) < 0555;
		}

		/**
		 * @return bool
		 * @throws Exception
		 */
		public function isReadable(){
			if($this->real_path) return $this->getAdapter()->is_readable($this->real_path);
			else return true;
		}

		/**
		 * @return bool
		 * @throws Exception
		 */
		public function isExecutable(){
			if($this->real_path) return $this->getAdapter()->is_executable($this->real_path);
			else return true;
		}



		/**
		 * @param $newPermissions
		 * @return $this
		 */
		public function setPermissions($newPermissions){
			$this->getPermissions()->setPermissions($newPermissions);
			return $this;
		}

		/**
		 * @param null $octal if null will be return Permissions object
		 * @return int|Permissions|string
		 * @throws Exception
		 */
		public function getPermissions($octal = null){
			if($this->permissions === null){
				$this->permissions = (new Permissions(function($decimal){
					if($this->isActual()){
						if(!@$this->getAdapter()->chmod($this->real_path,$decimal)){
							$e = error_get_last();
							throw new ActionError(
								sprintf('Error change permissions (Path: "%s", Message: "%s") ',$this->getAbsolutePath(), $e['message']));
						}
						return true;
					}else{
						return false;
					}
				}
				));
				if($this->real_path){
					if(!($permissions = @$this->getAdapter()->fileperms($this->real_path))){
						$e = error_get_last();
						throw new Exception(sprintf('File permissions(%s) get error: %s',$this->getAbsolutePath(),$e['message']));
					}
					$this->permissions->setPermissions($permissions,false, true);
				}else{
					$this->permissions->setPermissions($this->getDefaultPermissions(),false, false);
				}
			}
			return $octal!==null? $this->permissions->getPermissions($octal): $this->permissions;
		}

		abstract protected function getDefaultPermissions();

		/**
		 * @param $owner
		 * @return $this
		 */
		public function setOwner($owner){

			return $this;
		}

		public function getOwner(){
			return $this->owner;
		}

		/**
		 * @param $group
		 * @return $this
		 */
		public function setGroup($group){

			return $this;
		}

		public function getGroup(){
			return $this->group;
		}

		/**
		 * @return bool
		 */
		public function isFile(){
			return false;
		}

		/**
		 * @return bool
		 */
		public function isDir(){
			return false;
		}

		/**
		 * @return null
		 */
		public function getAccessTime(){
			if($this->real_path){
				return $this->getAdapter()->fileatime($this->real_path);
			}
			return null;
		}

		/**
		 * @return null
		 */
		public function getModifyTime(){
			if($this->real_path){
				return $this->getAdapter()->filemtime($this->real_path);
			}
			return null;
		}

		/**
		 * @return null
		 */
		public function getCreateTime(){
			if($this->real_path){
				return $this->getAdapter()->filectime($this->real_path);
			}
			return null;
		}

		/**
		 * @param null $modifyTime
		 * @param null $accessTime
		 * @return null
		 */
		public function touch($modifyTime = null, $accessTime = null){
			if($this->real_path){
				return $this->getAdapter()->touch($this->real_path, $modifyTime, $accessTime);
			}
			return null;
		}

		/**
		 * @param $basename
		 * @return $this
		 * @throws Exception
		 */
		public function setBasename($basename){
			if(!$basename){
				throw new Exception('Name is must be valid');
			}
			if(!$this->compareName($basename) && $this->_beforeRename($basename)!==false){
				if($this->isActual()) $this->_rename($basename);
				$this->basename         = $basename;
				if($this->isActual())$this->_afterBasenameChange();

			}
			return $this;
		}

		protected function _afterBasenameChange(){
			if($this->real_path){
				$this->real_path = dirname($this->real_path) . $this->getAdapter()->ds() . $this->basename;
			}
		}

		/**
		 * @return string
		 */
		public function getBasename(){
			return $this->basename;
		}

		/**
		 * @param $name
		 * @throws Exception
		 */
		protected function _beforeRename($name){
			$parent = $this->getParent();
			if($parent && $parent->hasNode($name)){
				throw new Exception("Node {$name} already exists in {$parent->basename}({$parent->getAbsolutePath()})");
			}
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function compareName($name){
			return strcasecmp($name, $this->basename)===0;
		}

		/**
		 * @param $pattern
		 * @param bool $regExp
		 * @return bool
		 */
		public function matchName($pattern,$regExp = false){
			return $regExp?boolval(preg_match($regExp, $this->basename)):fnmatch($pattern, $this->basename);
		}

		/**
		 * @param Manager $manager
		 * @return $this
		 */
		public function setManager(Manager $manager){
			if($this->manager !== $manager){
				$this->manager = $manager;
			}
			return $this;
		}

		/**
		 * @return Manager
		 */
		public function getManager(){
			return $this->manager;
		}

		/**
		 * @return Manager\Adapter
		 * @throws Exception
		 */
		public function getAdapter(){
			if(!$this->manager){
				throw new Exception('Adapter is not set');
			}
			$adapter = $this->manager->getAdapter();
			if(!$adapter){
				throw new Exception('Adapter is not set in SignManager');
			}
			return $adapter;
		}

		/**
		 * @return null|string
		 */
		public function getRealPath(){
			return $this->real_path;
		}

		/**
		 * @param null $realPath
		 * @return null|string
		 * @throws Exception
		 */
		public function getAbsolutePath($realPath = null){
			if(!$realPath)$realPath = $this->real_path;
			return $realPath? $this->getAdapter()->absolute($realPath) : null;
		}

		/**
		 * @return bool
		 * Или Не актуальная нода
		 * Нода является фантомной если:
		 * Объект был отсоединен(Перемещение).
		 * Объект был клонирован(Копирование).
		 * Объекта не существует в файловой системе (Не создан)
		 */
		public function isPhantom(){
			return $this->moving | $this->cloning | !$this->exists;
		}

		/**
		 * @see isPhantom negated
		 * @return bool
		 */
		public function isActual(){
			return $this->exists && !$this->moving && !$this->cloning;
		}

		/**
		 * @return bool
		 */
		public function isNew(){
			return !$this->real_path && !$this->exists;
		}



		/**
		 * @return Directory|File
		 * Вернет самый верхний загруженый элемент
		 * иерархии в которой находиться текущий объект
		 */
		protected function getRoot(){
			if(!$this->parent){
				return $this;
			}elseif($this->parent){
				return $this->parent->getRoot();
			}else{
				return null;
			}
		}

		/**
		 * @return bool
		 * Проверяет является ли самый верхний загруженный по иерархии объект - фантомным
		 */
		protected function isRootPhantom(){
			if(!$this->parent){
				return $this->isPhantom();
			}else{
				return $this->parent->isRootPhantom();
			}
		}

		/**
		 * @param bool $directoryOnly
		 * @return Directory|File
		 * Вернет самый верхний загруженый элемент иерархии Только если он является фантомным
		 */
		protected function getRootPhantom($directoryOnly = true){
			if(!$this->parent && $this->isPhantom()){
				if($directoryOnly && $this instanceof Directory){
					return $this;
				}else{
					return null;
				}
			}elseif($this->parent){
				return $this->parent->getRootPhantom();
			}else{
				return null;
			}
		}

		/**
		 * @return Directory|File
		 *
		 * Вернет верхний по иерархии , но самый близкий к текущему объекту - актуальный объект
		 */
		protected function getTopActual(){
			if($this->isActual()){
				return $this;
			}elseif($this->parent){
				return $this->parent->getTopActual();
			}else{
				return null;
			}
		}

		/**
		 * Обновление Ноды и вложеных в неё детей
		 */
		protected function update(){
			if($this->parent && $this->isPhantom() && !$this->parent->isPhantom()){
				if($this->getAdapter() !== $this->parent->getAdapter()){
					$this->transfer($this->getAdapter(),$this->parent->getAdapter());
				}else{
					$this->actualize();
				}
			}
		}


		protected function transfer(Adapter $from, Adapter $to){

		}

		/**
		 * @param int $depth
		 *
		 * Актуализация работает посредством обхода всех загруженых дочерних нод
		 *
		 *
		 */
		protected function actualize($depth = 0){
			$parent = $this->parent;
			$permissions = $this->getPermissions();
			$this->propagateCopy($parent);
			$this->propagateMove($parent, (!$depth));
			$this->propagateCreate($parent);
			$permissions->applyChanges();
		}

		/**
		 * @return Directory
		 * Если текущий объект фантомный то родитель не загружается
		 * т.к он изначально был уже загружен
		 */
		public function getParent(){
			if(!$this->parent && !$this->isPhantom()){
				$this->_loadParent();
			}
			return $this->parent;
		}

		/**
		 * @param Directory|null $parent
		 * @param bool $appliedInNew
		 * @param bool $appliedInOld
		 * @param bool $mutable
		 * @param bool $mutableAll
		 * @return $this ;
		 */
		public function setParent(Directory $parent = null, $appliedInNew = false, $appliedInOld = false, $mutable = false, $mutableAll = false){
			$old = $this->parent;
			if($old !== $parent && ($mutable || $this->_beforeParentChange($parent)!==false) && ($appliedInNew || !$parent || $this->_checkAlreadyExistsIn($parent)!==false)){

				$this->parent = $parent;

				if($old){
					if(!$appliedInOld) $old->detachNode($this,true,$mutable,$mutableAll);
					if(!$mutableAll)$this->onDetached($old);
				}

				if($parent){
					if(!$appliedInNew) $parent->addNode($this,true,$mutable,$mutableAll);
					if(!$mutableAll)$this->onAttached($parent);
				}
			}
		}

		/**
		 * @param Directory $expected
		 * @throws AlreadyExistsIn
		 */
		protected function _checkAlreadyExistsIn(Directory $expected){
			if($expected->hasNode($this->basename)){
				throw new AlreadyExistsIn("Add node Error: Already exists node \"{$this->basename}\" in {$expected->basename}({$expected->getAbsolutePath()})");
			}
		}

		/**
		 * @param Directory $expected
		 * @throws ProcessLock
		 */
		protected function _beforeParentChange(Directory $expected = null){
			if($expected){
				if($expected->cloning && !$this->isNew()){
					throw new ProcessLock("Завершите копирование {$expected->basename}(Реальная копия: {$expected->getAbsolutePath()}) прежде чем добавлять в него {$this->basename}  ");
				}

				if($expected->moving && !$this->isNew()){
					throw new ProcessLock("Завершите перемещение {$expected->basename}({$expected->getAbsolutePath()}) прежде чем добавлять в него {$this->basename}");
				}

				if($expected->isNew() && !$this->isNew()){
					throw new ProcessLock("Для начала реализуйте создание {$expected->basename}({$expected->getAbsolutePath()}) прежде чем добавлять в него {$this->basename}");
				}
			}
		}

		/**
		 * @param Directory $parent
		 * Происходит присоединение текущей иерархии (Которая являлась висячей)
		 *
		 *      Если эта иерархия помечена CLONED, и @see $parent реально существующий
		 *      - то произойдет CLONE PROCESS для текущей иерархии
		 *
		 *
		 * */
		protected function onAttached(Directory $parent){
			$this->update();
		}

		/**
		 * @param Directory $parent
		 *
		 * Происходит отсоединение текущей иерархии
		 * (Полиморфизм в фантомный объект.
		 *      Если иерархия является существующей реально
		 *      - то при присоединении к другой РЕАЛЬНОЙ ДИРЕКТОРИИ, иерархия реализует @see _move.
		 *      если другая Directory, является фантомной , то при получении PROPAGATE ВЫЗОВА , все DETACHED Вложеные в ту директорию
		 *      Ноды должны будут реализовать Move или Clone .
		 *
		 *      Если иерархия не является существующей реально
		 *      - то при присоединении куда либо, ничего не произойдет пока не потребуется создание вложеных нод.
		 *      тоесть иерархия остается так-же фантомом до какого-то момента где потребуется создание нод
		 * )
		 **/
		protected function onDetached(Directory $parent){
			if(!$this->deleted){
				$this->moving = true;
			}
		}

		/**
		 * @Copy
		 */
		public function __clone(){
			$this->parent = null;
			$this->cloning = true;
			if($this->permissions instanceof Permissions){
				$this->permissions = clone $this->permissions;
				$applyFn = $this->permissions->getApplyFunc();
				if($applyFn){
					$this->permissions->setApplyFunc(\Closure::bind($applyFn,$this));
				}
			}
			$this->getManager()->regNode($this);
		}


		/**
		 * @param Directory $parent
		 * @param bool $doCopy
		 */
		protected function propagateCopy(Directory $parent, $doCopy = true){
			if($this->cloning && $this->real_path){
				if($parent->getManager() !== $this->getManager()){
					$this->transferCopy($parent->getManager());
				}else{
					$actual_path        = $parent->real_path . $this->getAdapter()->ds() . $this->basename;
					if($doCopy && !$this->getAdapter()->file_exists($actual_path)){
						$this->_copy($actual_path);
					}
					$this->cloning      = false;
					$this->moving       = false;
					$this->exists       = true;
					$this->real_path    = $actual_path;
				}
			}
		}

		protected function transferCopy(Manager $manager){

			$adapter = $manager->getAdapter();
			$oldAdapter = $this->getAdapter();


			ftp_fput();

		}

		/**
		 * @param Directory $parent
		 * @param bool $doMove
		 */
		protected function propagateMove(Directory $parent, $doMove = true){
			if($this->moving && $this->real_path){
				if($parent->getManager() !== $this->getManager()){
					$this->transferMove($parent->getManager());
				}else{
					$actual_path        = $parent->real_path . $this->getAdapter()->ds() . $this->basename;
					if($doMove && $this->getAdapter()->file_exists($this->real_path)){
						$this->_move($actual_path);
					}
					$this->moving       = false;
					$this->real_path    = $actual_path;
				}
			}
		}

		protected function transferMove(Manager $manager){

		}


		/**
		 * @param Directory $parent
		 */
		protected function propagateCreate(Directory $parent){
			if(!$this->exists){
				$actual_path        = $parent->real_path . $this->getAdapter()->ds() . $this->basename;
				$this->_create($actual_path);
				$this->exists       = true;
				$this->real_path    = $actual_path;
			}
		}


		/**
		 * @return $this
		 * @throws ActionError
		 */
		public function create(){
			if($this->isNew()){
				if(!($dir = $this->getParent())){
					throw new ActionError('Нельзя реализовать создание объекта у которого в цепочке родительских директорий не существует реальной директории (Ошибка: висячая иерархия)');
				}

				$path = $dir->create(true)->real_path . $this->getAdapter()->ds() . $this->basename;
				$this->_create($path);
				$permissions = $this->getPermissions();
				$this->real_path    = $path;
				$this->exists       = true;
				$permissions->applyChanges();
				//TODO Owner, Group - after create
			}
			return $this;
		}

		/**
		 * @param bool $forceDelete Удаление проигнорировав запреты прав доступа.
		 * Попробует снять с Ноды запреты на удаление и все-равно удалит файл или директорию
		 * @param bool $ignoreError
		 * @param Node $deletionRoot
		 * @return $this
		 * @throws ActionError
		 * @throws Exception
		 * @throws \Exception
		 */
		public function delete($forceDelete = false, $ignoreError = false,Node $deletionRoot = null){
			if(!$deletionRoot)$deletionRoot = $this;
			if($this->exists && ($this->_beforeDelete($forceDelete,$ignoreError,$deletionRoot)!==false)){
				$toDelete = true;
				if(!$this->cloning){
					if($forceDelete){
						$this->getPermissions()->setPermissions(0777);
					}
					try{
						$this->_delete();
					}catch(ActionError $e){
						if($ignoreError && !$forceDelete){
							$toDelete = false;
						}elseif($forceDelete){
							throw $e;
						}else{
							throw $e;
						}
					}

				}
				if($toDelete){
					$this->exists       = false;
					$this->real_path    = null;
					$this->deleted      = true;
					if($this->manager){
						$this->manager->unregNode($this);
						$this->manager = null;
					}
					if($this->parent){
						$this->setParent(null);
					}
				}
			}
			return $this;
		}

		/**
		 * @param bool $forceDelete
		 * @param bool $ignoreError
		 * @param Node $deletionRoot
		 * @return bool|null
		 * @throws ActionError
		 */
		protected function _beforeDelete($forceDelete = false, $ignoreError = false,$deletionRoot = null){
			if(!$forceDelete && !$ignoreError && !$this->isDeletable()){
				throw new ActionError('Could not remove "'.$this->getAbsolutePath().'", detected contains remove permissions access denied');
			}
		}


		/**
		 * @param $path
		 * @return bool
		 */
		abstract protected function checkExistingNodeType($path);


		/**
		 * @param string $path
		 * @return mixed
		 */
		abstract protected function _create($path);

		/**
		 * Based from @see real_path
		 * @param string $name
		 * @return mixed
		 * @throws ActionError
		 */
		protected function _rename($name){
			$newPath = dirname($this->real_path).$this->getAdapter()->ds().basename($name);
			if(!@$this->getAdapter()->rename($this->real_path, $newPath)){
				$e = error_get_last();
				throw new ActionError(sprintf('Could not rename "%s" to "%s" for renaming node(%s - %s), message: %s',$this->getAbsolutePath(),$this->getAbsolutePath($newPath) , $this->basename, $name , $e['message']));
			}
		}

		/**
		 * Based from @see real_path
		 * @param string $newNodePath
		 * @return mixed
		 * @throws ActionError
		 */
		protected function _move($newNodePath){
			if(!@$this->getAdapter()->rename($this->real_path, $newNodePath)){
				$e = error_get_last();
				throw new ActionError(sprintf('Could not move(rename absolute) node "%s" to "%s", message: %s',$this->getAbsolutePath(), $this->getAbsolutePath($newNodePath)  , $e['message']));
			}
		}

		/**
		 * Based from @see real_path
		 * @param string $destinationPath
		 * @return mixed
		 */
		abstract protected function _copy($destinationPath);

		/**
		 * Based from @see real_path
		 * @return mixed
		 */
		abstract protected function _delete();

		/**
		 * @throws Exception
		 */
		protected function _loadParent(){
			if($this->exists){
				$parentPath = dirname($this->real_path);
				if($parentPath){
					$this->parent = $this->getManager()->get($parentPath);
					$this->parent->_loadedAsParentFrom($this);
				}else{
					throw new Exception('Could not load parent, because "'.$this->getAbsolutePath().'" is root.');
				}
			}
		}

		/**
		 * @For Implement in @see Directory._loadParentReverse()
		 * @param Node $child
		 * @throws Exception
		 */
		protected function _loadedAsParentFrom(Node $child){
			throw new Exception('_loadParentReverse Метод является вспомогательным protected методом, реализация его находится в Directory для добавления в родителя _loadParent вызываемого');
		}


		/**
		 * @param $key
		 * @param $value
		 * @throws Exception
		 */
		public function __set($key,$value){
			switch($key){
				case 'permissions':
					$this->setPermissions($value);
					break;
				case 'owner':
					$this->setOwner($value);
					break;
				case 'group':
					$this->setGroup($value);
					break;
				case 'parent':
					$this->setParent($value);
					break;
				case 'basename':
					$this->setBasename($value);
					break;
				case 'name':
					$this->setBasename($value);
					break;
				default:
					throw new Exception('Set property "'.$key.'" not allowed.');
					break;
			}
		}

		/**
		 * @param $key
		 * @return int|Permissions|null|string
		 * @throws Exception
		 */
		public function __get($key){
			switch($key){

				case 'permissions':
					return $this->getPermissions();
					break;
				case 'size':
					return $this->getSize();
					break;
				case 'owner':
					return $this->getOwner();
					break;
				case 'group':
					return $this->getGroup();
					break;

				case 'created_time':
					return $this->getCreateTime();
					break;

				case 'access_time':
					return $this->getAccessTime();
					break;

				case 'modify_time':
					return $this->getModifyTime();
					break;

				case 'manager':
					return $this->getManager();
					break;
				case 'adapter':
					return $this->getAdapter();
					break;
				case 'parent':
					return $this->getParent();
					break;
				case 'basename':
					return $this->basename;
					break;
				case 'name':
					return $this->basename;
					break;
				case 'path':
					return $this->real_path;
					break;

				case 'absolute_path':
					return $this->getAbsolutePath();
					break;

				default:
					throw new Exception('Get property "'.$key.'" not found in class.');
					break;
			}
		}



		public function __isset($key){}

		public function __unset($key){}



	}
}

