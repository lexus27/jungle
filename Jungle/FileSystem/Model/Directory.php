<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 02.02.2016
 * Time: 21:40
 */
namespace Jungle\FileSystem\Model {

	use Jungle\FileSystem\Model\Exception\ActionError;
	use Jungle\FileSystem\Model\Exception\AlreadyExistsIn;
	use Jungle\FileSystem\Model\Exception\ProcessLock;

	/**
	 * Class Directory
	 * @package Jungle\FileSystem\Model
	 */
	class Directory extends Node implements \ArrayAccess,\IteratorAggregate, \Countable{

		/** @var Node[] */
		protected $children = [];

		/** @var int */
		protected $count = null;

		/** @var array */
		protected $detached = [];

		/**
		 * @return bool
		 */
		public function isDir(){
			return false;
		}

		/**
		 * @return bool
		 */
		public function isEmpty(){
			return !$this->count();
		}

		/**
		 * @return int|null
		 */
		public function getSize(){
			$size = 0;
			foreach($this->getChildren() as $n){
				$size+= $n->getSize();
			}
			return $size;
		}

		/**
		 *
		 */
		public function getDiskFreeSpace(){

		}

		public function __get($key){
			switch($key){

				case 'policies':
					return $this->permissions;
					break;
				case 'directories':
					return $this->owner;
					break;
				case 'files':
					return $this->group;
					break;

				case 'parent':
					return $this->getParent();
					break;
				case 'name':
					return $this->basename;
					break;
				case 'path':
					return $this->real_path;
					break;

				default:
					throw new Exception('Property access "'.$key.'" not found in class.');
					break;
			}
		}

		/**
		 * @throws Exception
		 */
		protected function _afterBasenameChange(){
			parent::_afterBasenameChange();
			foreach($this->children as $n){
				if($n->exists && $n->real_path){
					$n->real_path = $this->real_path . $this->getAdapter()->ds() . $n->basename;
				}
			}
		}

		/**
		 * Метод для загрузки родителя из @param Node $child
		 * @param Node $child
		 */
		protected function _loadedAsParentFrom(Node $child){
			$this->children[] = $child;
		}

		/**
		 * @param $path
		 * @return bool
		 */
		protected function checkExistingNodeType($path){
			return $this->getAdapter()->is_dir($path);
		}

		/**
		 * @param bool|false $onlySelf
		 * @return $this
		 */
		public function create($onlySelf = false){
			parent::create();
			if(!$onlySelf){
				foreach($this->children as $child){
					$child->create();
				}
			}
			return $this;
		}

		/**
		 * Обновление всех
		 */
		protected function update(){
			$this->_clearAllDeleted();
			gc_collect_cycles();
			parent::update();
		}

		/**
		 * @param int $depth
		 */
		protected function actualize($depth = 0){
			parent::actualize($depth);
			foreach($this->children as $child){
				$child->actualize($depth + 1);
			}
		}
		protected function getDefaultPermissions(){
			return $this->getManager()->getDefaultDirPermissions();
		}

		/**
		 * @Clone
		 */
		public function __clone(){
			parent::__clone();
			foreach($this->getChildren() as & $child){
				$child          = clone $child;
				$child->parent  = $this;
			}
		}

		/**
		 * @param string $path
		 * @return mixed
		 * @throws Exception
		 * @throws Exception\ActionError
		 */
		protected function _create($path){
			if(!@$this->getAdapter()->mkdir($path,0777)){
				$e = error_get_last();
				throw new Exception\ActionError(sprintf('Error create dirInclude "%s" , message: %s',$path,$e['message']));
			}
		}

		/**
		 * Based from @see real_path
		 * @param string $destinationPath
		 * @return mixed
		 * @throws Exception
		 * @throws Exception\ActionError
		 */
		protected function _copy($destinationPath){
			if(!@$this->getAdapter()->mkdir($destinationPath,0777)){
				$e = error_get_last();
				throw new Exception\ActionError(sprintf('Could not copyNode dirInclude from "%s" to "%s", message: %s',$this->real_path, $destinationPath , $e['message']));
			}
		}

		/**
		 * Based from @see real_path
		 * @return mixed
		 * @throws Exception
		 * @throws Exception\ActionError
		 */
		protected function _delete(){
			if(!@$this->getAdapter()->rmdir($this->real_path)){
				$e = error_get_last();
				throw new Exception\ActionError(sprintf('Could not remove dir "%s", message: %s',$this->real_path , $e['message']));
			}
		}



		/**
		 * @param bool $forceDelete
		 * @param bool $ignoreError
		 * @param bool $deletionRoot
		 * @return bool
		 * @throws ActionError
		 * @throws \Exception
		 * @internal param bool $___expandAllowed
		 */
		protected function _beforeDelete($forceDelete=false,$ignoreError=false, $deletionRoot=null){
			if(parent::_beforeDelete($forceDelete,$ignoreError,$deletionRoot)===false){
				return false;
			}
			if($deletionRoot===$this && !$forceDelete && !$ignoreError){
				foreach($this->expand(true) as $child){
					if(!$child->isDeletable()){
						throw new ActionError('Could not remove "'.$this->getAbsolutePath().'", detected contains remove permissions access denied');
					}
				}
			}
			foreach($this->getChildren() as $child){
				$child->delete($forceDelete,$ignoreError,$deletionRoot);
			}
			return true;
		}


		/**
		 * @param bool|false $directoryInclude
		 * @return int
		 */
		public function countContains($directoryInclude = false){
			$c = 0;
			foreach($this->expand($directoryInclude) as $s){
				$c++;
			}
			return $c;
		}


		/**
		 * @param $relativePath
		 * @return bool
		 */
		public function pathExists($relativePath){
			$chunks = preg_split('@[\\\\/]+@',trim($relativePath,'\\/'));
			$node = $this;
			while(($chunk = array_shift($chunks))){
				if($chunk === '.'){
					continue;
				}elseif($chunk === '..'){
					$node = $node->getParent();
					if(!$node){
						return false;
					}
				}elseif(!$node->hasNode($chunk)){
					return false;
				}
			}
			return true;
		}

		/**
		 * @param $relativePath
		 * @return Directory|File|Node|null
		 * @throws Exception
		 */
		public function pathQuery($relativePath){
			$chunks = preg_split('@[\\\\/]+@',$relativePath);
			$node = $this;
			while(($chunk = array_shift($chunks))){
				if($chunk === '.'){
					continue;
				}elseif($chunk === '..'){
					$node = $node->getParent();
					if(!$node){
						return null;
					}
				}elseif($node->hasNode($chunk)){
					$node = $node->getNode($chunk);
				}else{
					return null;
				}
			}
			return $node;
		}

		/**
		 * Clear all deleted nodes
		 */
		protected function _clearAllDeleted(){
			foreach($this->children as $i => $child){
				if($child instanceof Directory){
					$child->_clearAllDeleted();
				}
				if($child->deleted){
					$child->manager = null;
					$this->detachNode($child);
				}
			}
		}


		/**
		 * @return int
		 */
		public function count(){
			return $this->count!==null?$this->count:count($this->_nodeNames());
		}


		/**
		 * @param Directory $directory
		 */
		public function extractTo(Directory $directory){
			foreach($this->getChildren() as $node){
				$directory->addNode($node);
			}
		}

		/**
		 * Очистка от вложеных нод
		 */
		public function clear(){
			foreach($this->getChildren() as $node){
				$this->removeNode($node);
			}
			$this->refresh();
		}

		/**
		 *
		 */
		public function refresh(){
			$this->children = [];
			gc_collect_cycles();
		}

		/**
		 * @param Node $node
		 * @return bool
		 */
		public function isContain(Node $node){
			foreach($this->getChildren() as $n){
				if($n === $node || $n->isDir() && $n->isContain($node)){
					return true;
				}
			}
			return false;
		}


		/**
		 * @param bool $directoryInclude
		 * @param bool $directoryAfterChildren
		 * @param callable $checker
		 * @return File[]|Directory[]
		 * @throws Exception
		 */
		public function &expand($directoryInclude = false, $directoryAfterChildren = false, callable $checker = null){
			$names = $this->getNodeNames();
			if($checker){
				foreach($names as $name){
					if(($child = & $this->getNode($name))){
						if($child instanceof Directory){
							if(call_user_func($checker, $child, true)){
								if($directoryInclude && !$directoryAfterChildren){
									yield $child;
								}
								foreach($child->expand($directoryInclude, $directoryAfterChildren, $checker) as & $ch){
									yield $ch;
								}
								if($directoryInclude && $directoryAfterChildren){
									yield $child;
								}
							}
						}elseif(call_user_func($checker,$child, false)){
							yield $child;
						}
					}
				}
			}else{
				foreach($names as $name){
					if(($child = & $this->getNode($name))){
						if($child instanceof Directory){
							if($directoryInclude && !$directoryAfterChildren){
								yield $child;
							}
							foreach($child->expand($directoryInclude, $directoryAfterChildren, $checker) as & $ch){
								yield $ch;
							}
							if($directoryInclude && $directoryAfterChildren){
								yield $child;
							}
						}else{
							yield $child;
						}
					}
				}
			}

		}

		/**
		 * @param bool|true $childrenDirAfter
		 * @return Directory[]
		 * @throws Exception
		 */
		public function expandAllDirectories($childrenDirAfter = true){
			$names = $this->getNodeNames();
			foreach($names as $name){
				if($this->isDirNode($name) && ($node = & $this->getNode($name))){
					if($childrenDirAfter)yield $node;
					foreach($node->expandAllDirectories() as $dir){
						yield $dir;
					}
					if(!$childrenDirAfter)yield $node;
				}
			}
		}

		/**
		 * @return File[]
		 * @throws Exception
		 */
		public function expandAllFiles(){
			$names = $this->getNodeNames();
			foreach($names as $name){
				if(($node = & $this->getNode($name))){
					if($node instanceof Directory){
						foreach($node->expandAllFiles() as $dir){
							yield $dir;
						}
					}else{
						yield $node;
					}
				}
			}
		}



		/**
		 * @param Node $node
		 * @param bool|false $appliedParentInNode
		 * @param bool $mutable
		 * @param bool $mutableAll
		 * @return $this
		 */
		public function addNode(Node $node, $appliedParentInNode = false, $mutable = false,$mutableAll = false){
			if($this->searchLoadedNode($node) === false && ($mutable || $this->_beforeAddNode($node)!==false) && ($appliedParentInNode || $this->_checkNodeAlreadyExists($node)!==false)){
				$this->children[] = $node;
				if(!$appliedParentInNode){
					$node->setParent($this, true,false,$mutable,$mutableAll);
				}
				$this->count = $this->count() + 1;
				if(in_array($node->basename,$this->detached,true)){
					$this->detached = array_diff($this->detached, [$node->basename]);
				}
			}
			return $this;
		}

		/**
		 * @param Node $node
		 * @throws AlreadyExistsIn
		 */
		protected function _checkNodeAlreadyExists(Node $node){
			if($this->hasNode($node->basename)){
				throw new AlreadyExistsIn("Add node Error: Already exists node \"{$node->basename}\" in {$this->basename}({$this->getAbsolutePath()})");
			}
		}

		/**
		 * @param Node $expected
		 * @throws ProcessLock
		 */
		protected function _beforeAddNode(Node $expected){
			if($this->cloning && !$expected->isNew()){
				throw new ProcessLock("Завершите копирование {$this->basename}(Реальная копия: {$this->getAbsolutePath()}) прежде чем добавлять в него {$expected->basename}  ");
			}

			if($this->moving && !$expected->isNew()){
				throw new ProcessLock("Завершите перемещение {$this->basename}({$this->getAbsolutePath()}) прежде чем добавлять в него {$expected->basename}");
			}

			if($this->isNew() && !$expected->isNew()){
				throw new ProcessLock("Для начала реализуйте создание {$this->basename}({$this->getAbsolutePath()}) прежде чем добавлять в него {$expected->basename}");
			}
		}

		/**
		 * @param Node $node
		 * @return mixed
		 */
		public function searchLoadedNode(Node $node){
			return array_search($node , $this->children , true);
		}

		/**
		 * @param Node|string $node , string
		 * @param bool|false $appliedParentInNode
		 * @param bool $mutable
		 * @param bool $mutableAll
		 * @return Directory|file
		 * @throws Exception
		 */
		public function detachNode($node, $appliedParentInNode = false, $mutable = false,$mutableAll = false){
			if(!$node){
				throw new Exception('Node name must be not empty!');
			}
			if(!$node instanceof Node){
				$node = $this->getNode($node);
			}
			if($node){
				if(($i = $this->searchLoadedNode($node)) !== false){
					array_splice($this->children,$i,1);
					if(!$appliedParentInNode){
						$node->setParent(null, true, true,$mutable,$mutableAll);
					}
					if(!in_array($node->basename,$this->detached,true) && !$node->deleted){
						$this->detached[] = $node->basename;
					}
				}
				return $node;
			}
			return null;
		}

		/**
		 * @param $name
		 * @return $this
		 * @throws Exception
		 */
		public function removeNode($name){
			if($name instanceof Node){
				if(!$name->parent || $name->parent !== $this){
					throw new Exception('Passed Node to removeNode is not child in that Directory');
				}
				$node = $name;
			}else{
				$node = $this->getNode($name);
			}
			if($node){
				$node->delete();
			}
			return $this;
		}

		/**
		 * @param $name
		 * @param $newName
		 * @return Directory|File
		 * @throws Exception
		 */
		public function copyNode($name, $newName){
			$node = $this->getNode($name);
			if(!$node){
				throw new Exception("CopyNode: Origin node not found by name \"$name\" in $this->basename({$this->getAbsolutePath()})");
			}
			$copy = clone $node;
			$copy->setName($newName);
			$copy->setParent($this);
			return $copy;
		}


		/**
		 * @param $name
		 * @param bool $loadedNode
		 * @return bool
		 */
		public function hasNode($name, & $loadedNode = null){
			if($name instanceof Node){
				$name = $name->basename;
			}
			foreach($this->children as $child){
				if($child->compareName($name)){
					$loadedNode = $child;
					return true;
				}
			}
			return $this->_nodeExists($name);
		}

		/**
		 * @param $name
		 * @param bool $loadedNode
		 * @return bool
		 */
		public function isDirNode($name, & $loadedNode = null){
			if($name instanceof Node){
				$name = $name->basename;
			}
			foreach($this->children as $child){
				if($child->compareName($name)){
					$loadedNode = $child;
					return $child instanceof Directory;
				}
			}
			return $this->_nodeIsDir($name);
		}

		/**
		 * @param $name
		 * @param bool $loadedNode
		 * @return bool
		 */
		public function isFileNode($name, & $loadedNode = null){
			if($name instanceof Node){
				$name = $name->basename;
			}
			foreach($this->children as $child){
				if($child->compareName($name)){
					$loadedNode = $child;
					return $child instanceof File;
				}
			}
			return $this->_nodeIsFile($name);
		}

		/**
		 * @param $name
		 * @return Directory|File|Node|null
		 * @throws Exception
		 */
		public function &getNode($name){
			if($name instanceof Node){
				if(!$name->parent || $name->parent !== $this){
					throw new Exception('Passed Node to getNode is not child in that Directory');
				}
				return $name;
			}
			foreach($this->children as & $child){
				if($child->compareName($name)){
					return $child;
				}
			}
			if(($node = $this->_nodeLoad($name))){

				/**
				 * post load check
				 */
				$node->moving = $this->moving;
				$node->cloning = $this->cloning; //TODO Проверить на баги

				$i = count($this->children);
				$this->children[$i] = $node;
				$node->setParent($this,true,false,true);
				return $this->children[$i];
			}

			return null;
		}

		/**
		 * @param string $name
		 * @param bool|false $overwrite
		 * @return File
		 * @throws AlreadyExistsIn
		 * @throws Exception
		 */
		public function newFile($name, $overwrite = false){
			if(!$this->getManager()->isValidName($name)){
				throw new Exception('passed Name is invalid');
			}
			if($this->isDirNode($name, $node)){
				throw new AlreadyExistsIn('Could not create file, node name %s busy. On this relativePath already exists dirInclude');
			}
			if(!$node){
				if($this->_nodeIsFile($name)){
					if($overwrite){
						$node = $this->getNode($node);
						$node->overwrite();
					}else{
						throw new AlreadyExistsIn("Node($name) is already exists in Directory{{$this->basename}} from relativePath: {$this->getAbsolutePath()}");
					}
				}
			}else{
				if($node instanceof File){
					if($overwrite){
						$node->overwrite();
					}else{
						throw new AlreadyExistsIn("Node($name) is already exists in Directory{{$this->basename}} from relativePath: {$this->getAbsolutePath()}");
					}
				}
			}
			if(!$node instanceof File){
				$node = $this->getManager()->file($name);
				$node->setParent($this,false,false,true);
			}


			return $node;
		}

		/**
		 * @param $name
		 * @param $overwrite
		 * @return Directory
		 * @throws AlreadyExistsIn
		 * @throws Exception
		 */
		public function newDir($name, $overwrite = false){
			if(!$this->getManager()->isValidName($name)){
				throw new Exception('passed Name is invalid');
			}
			if($this->isFileNode($name, $node)){
				throw new AlreadyExistsIn('Could not create dirInclude, node name %s busy. On this relativePath already exists file');
			}
			if($node instanceof Directory || $this->_nodeIsDir($name)){
				if($overwrite){
					$node->clear();
				}else{
					throw new AlreadyExistsIn("Node($name) is already exists in Directory{{$this->basename}} from relativePath: {$this->getAbsolutePath()}");
				}
			}
			if(!$node instanceof Directory){
				$node = $this->getManager()->dir($name);
				$node->setParent($this,false,false,true);
			}
			return $node;
		}


		/**
		 * @param callable $checker
		 * @return Directory[]|File[]
		 * @throws Exception
		 */
		public function &getChildren(callable $checker = null){
			$names = $this->getNodeNames();
			foreach($names as $name){
				if(($node = & $this->getNode($name))){
					if($checker){
						if(call_user_func($checker,$node)){
							yield $node;
						}
					}else{
						yield $node;
					}
				}
			}
		}

		/**
		 * @param callable $checker
		 * @return Directory[]
		 * @throws Exception
		 */
		public function getDirectories(callable $checker = null){
			$names = $this->getNodeNames();
			foreach($names as $name){
				if($this->isDirNode($name) && ($node = & $this->getNode($name))){
					if($checker){
						if(call_user_func($checker,$node)){
							yield $node;
						}
					}else{
						yield $node;
					}
				}
			}
		}

		/**
		 * @param callable $checker
		 * @return File[]
		 * @throws Exception
		 */
		public function getFiles(callable $checker = null){
			$names = $this->getNodeNames();
			foreach($names as $name){
				if($this->isFileNode($name) && ($node = & $this->getNode($name))){
					if($checker){
						if(call_user_func($checker,$node)){
							yield $node;
						}
					}else{
						yield $node;
					}
				}
			}
		}

		/**
		 * @param $name
		 * @return Directory|File|null
		 */
		protected function _nodeLoad($name){
			$manager    = $this->getManager();
			$path       = $this->real_path . DIRECTORY_SEPARATOR . $name;
			try{
				return $manager->get($path);
			}catch(\LogicException $e){
				return null;
			}
		}

		/**
		 * @param $name
		 * @return bool
		 */
		protected function _nodeExists($name){
			return $this->getAdapter()->file_exists($this->real_path . DIRECTORY_SEPARATOR . $name);
		}

		/**
		 * @param $name
		 * @return bool
		 */
		protected function _nodeIsDir($name){
			return $this->getAdapter()->is_dir($this->real_path . DIRECTORY_SEPARATOR . $name);
		}

		/**
		 * @param $name
		 * @return bool
		 */
		protected function _nodeIsFile($name){
			return $this->getAdapter()->is_file($this->real_path . DIRECTORY_SEPARATOR . $name);
		}

		/**
		 * @return array
		 */
		protected function _nodeNames(){
			$nodeList = $this->getAdapter()->nodeList($this->real_path);
			return array_diff(array_map(function($path){return basename($path);},$nodeList),$this->detached);
		}

		/**
		 * @return array
		 */
		protected function getNodeNames(){
			$names = [];
			if($this->real_path){
				$names = $this->_nodeNames();
			}
			foreach($this->children as $child){
				if(!in_array($child->basename,$names,true)){
					$names[] = $child->basename;
				}
			}
			return $names;
		}



		/**
		 * @return Directory[]|File[]
		 */
		public function getIterator(){
			return $this->getChildren();
		}

		/**
		 * @param string $offset
		 * @return bool
		 */
		public function offsetExists($offset){
			return $this->hasNode($offset);
		}

		/**
		 * @param string $offset
		 * @return Directory|File|Node|null
		 */
		public function offsetGet($offset){
			return $this->getNode($offset);
		}

		/**
		 * @param null $offset
		 * @param Node $value
		 * @throws Exception
		 */
		public function offsetSet($offset, $value){
			if($offset){
				throw new Exception('Directory not support push by not empty $offset, strict standarts!');
			}
			if(!$value instanceof Node){
				throw new Exception('Directory support push only for Node as alias for Directory.regNode(Node $node)!');
			}
			$this->addNode($value);
		}

		/**
		 * @param string $offset
		 */
		public function offsetUnset($offset){
			$this->removeNode($offset);
		}


		/**
		 * @param bool|false $recursive
		 * @return $this
		 */
		public function setReadOnly($recursive = false){
			$this->setPermissions(PermissionsInterface::PERMISSIONS_READ_ONLY_DIRECTORY);
			if($recursive){
				foreach($this->getChildren() as $n){
					$n->setReadOnly($recursive);
				}
			}
			return $this;
		}
	}
}

