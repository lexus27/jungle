<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 03.02.2016
 * Time: 3:18
 */
namespace Jungle\FileSystem\Model {

	use Jungle\FileSystem;
	use Jungle\FileSystem\Adapter\Adapter;

	/**
	 * Class Manager
	 * @package Jungle\FileSystem\Model
	 */
	class Manager{

		/** @var */
		protected $alias;

		/** @var Manager */
		protected $ancestor;

		/** @var Manager[]  */
		protected $descendants = [];

		/** @var Adapter */
		protected $adapter;

		/** @var int */
		protected $default_file_permissions = 0444;

		/** @var int */
		protected $default_dir_permissions  = 0555;

		/** @var Directory[]|File[] */
		protected $nodes   = [];

		/**
		 * @param Manager|null $manager
		 * @param bool|false $appliedInNew
		 * @param bool|false $appliedInOld
		 * @return $this
		 */
		public function setAncestor(Manager $manager = null,$appliedInNew = false, $appliedInOld = false){
			$old = $this->ancestor;
			if($this->ancestor !== $manager){
				$this->ancestor = $manager;
				if($manager && !$appliedInNew){
					$manager->addDescendant($this,true);
				}
				if($old && !$appliedInOld){
					$manager->removeDescendant($this,true);
				}
			}
			return $this;
		}

		/**
		 * @return Manager
		 */
		public function getAncestor(){
			return $this->ancestor;
		}

		/**
		 * @return Manager
		 */
		public function getRootAncestor(){
			if($this->ancestor){
				return $this->ancestor->getRootAncestor();
			}else{
				return $this;
			}
		}

		/**
		 * @return bool
		 */
		public function isRootAncestor(){
			return !$this->ancestor;
		}

		/**
		 * @param Manager $manager
		 * @param bool|false $appliedInParent
		 * @return $this
		 */
		public function addDescendant(Manager $manager, $appliedInParent = false){
			if($this->searchDescendant($manager)===false){
				$this->descendants[] = $manager;
				if(!$appliedInParent){
					$manager->setAncestor($this,true);
				}
			}
			return $this;
		}

		/**
		 * @param Manager $manager
		 * @return mixed
		 */
		public function searchDescendant(Manager $manager){
			return array_search($manager,$this->descendants,true);
		}

		/**
		 * @param Manager $manager
		 * @param bool|false $appliedInParent
		 * @return $this
		 */
		public function removeDescendant(Manager $manager, $appliedInParent = false){
			if(($i = $this->searchDescendant($manager))!==false){
				array_splice($this->descendants,$i,1);
				if(!$appliedInParent){
					$manager->setAncestor(null,false,true);
				}
			}
			return $this;
		}

		/**
		 * @param Adapter $adapter
		 * @return $this
		 */
		public function setAdapter(Adapter $adapter){
			$this->adapter = $adapter;
			return $this;
		}

		/**
		 * @return Adapter
		 */
		public function getAdapter(){
			if(!$this->adapter){
				throw new \LogicException('Adapter is not exists in manager');
			}
			return $this->adapter;
		}



		/**
		 * @param int $permissions
		 * @return $this
		 */
		public function setDefaultFilePermissions($permissions = 0444){
			$this->default_file_permissions = $permissions;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getDefaultFilePermissions(){
			return $this->default_file_permissions;
		}

		/**
		 * @param int $permissions
		 * @return $this
		 */
		public function setDefaultDirPermissions($permissions = 0555){
			$this->default_dir_permissions = $permissions;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getDefaultDirPermissions(){
			return $this->default_dir_permissions;
		}

		/**
		 * @param Node $node
		 * @return $this
		 */
		public function regNode(Node $node){
			if($this->isRootAncestor()){
				if($this->searchRegistered($node) === false){
					$this->nodes[] = $node;
				}
			}else{
				$this->getRootAncestor()->regNode($node);
			}
			return $this;
		}

		/**
		 * @param Node $node
		 * @return $this
		 */
		public function regExistingNode(Node $node){
			if(!($path = $node->getRealPath())){
				throw new \LogicException('Node is not exists');
			}
			if($this->find($path)===null){
				$this->nodes[] = $node;
			}
			return $this;
		}

		/**
		 * @param Node $node
		 * @return $this
		 */
		public function unregNode(Node $node){
			if(($i = $this->searchRegistered($node))!==false){
				array_splice($this->nodes, $i,1);
			}
			return $this;
		}

		/**
		 * @param Node $node
		 * @return mixed
		 */
		public function searchRegistered(Node $node){
			return array_search($node,$this->nodes,true);
		}

		/**
		 * @param $path
		 * @return Directory|File|null
		 */
		public function find($path){
			foreach($this->nodes as $node){
				if(fnmatch($path, $node->getRealPath())){
					return $node;
				}
			}
			return null;
		}

		/**
		 * @param string $pattern
		 * @param bool $regExp
		 * @return Directory[]|File[]
		 */
		public function collect($pattern,$regExp = false){
			$a = [];
			foreach($this->nodes as $node){
				if($regExp?preg_match($pattern,$node->getRealPath()):fnmatch($pattern, $node->getRealPath())){
					$a[] = $node;
				}
			}
			return $a;
		}

		/**
		 * @param $path
		 * @return int|bool
		 */
		public function search($path){
			foreach($this->nodes as $i => $node){
				if(fnmatch($path, $node->getRealPath())){
					return $i;
				}
			}
			return false;
		}

		/**
		 * @param $path
		 * @param bool $normalize
		 * @return Directory|File
		 */
		public function get($path, $normalize = true){
			$ds = $this->getAdapter()->ds();
			if($normalize){
				$path = trim(FileSystem::normalizePath($path,true,$ds),'\/');
			}
			$path = trim($path,'\/');

			// check tagged query
			if(substr($path,0,1) === '#'){
				$pos = strpos($path,$ds);
				if($pos!==false){
					$tag = substr($path, 1, $pos);
					$path = substr($path,$pos);
				}else{
					$tag = substr($path, 1);
					$path = null;
				}
				$dir = $this->getTagged($tag);
				if($dir && $path){
					return $dir->get($path);
				}
				return $dir;
			}

			return $this->findPath($path);
		}

		public function findPath($path){
			$node = $this->find($path);
			if($node!==null){
				return $node;
			}elseif($this->getAdapter()->file_exists($path)){
				return $this->load($path);
			}else{
				return null;
			}
		}

		/**
		 * @param $tag
		 * @return Directory|null
		 */
		public function getTagged($tag){
			foreach($this->nodes as $node){
				if($node instanceof Directory && $node->tag === $tag){
					return $node;
				}
			}
			return null;
		}

		/**
		 * @param $path
		 * @param bool|true $normalize
		 * @return $this|Directory|File|Node|null
		 */
		public function dir($path, $normalize = true){
			$ds = $this->getAdapter()->ds();
			if($normalize){
				$path = FileSystem::normalizePath($path, true, $ds);
			}
			$path = trim($path,'\/');
			$pos = strpos($path,$ds);
			if($pos!==false || ($pos = strpos($path,FileSystem::revertPathSeparator($ds))) !== false){
				$chunk = substr($path,0,$pos);
				$dir = $this->get($chunk, false);
				if(!$dir){
					$dir = $this->newDir($chunk);
				}
				return $dir->dir(substr($path,$pos), false);
			}else{
				$dir = $this->get($path, false);
				if(!$dir){
					$dir = $this->newDir($path)->create();
				}
				return $dir;
			}
		}

		/**
		 * @param $path
		 * @return Directory|File|Node|null
		 *
		 */
		public function file($path){
			$basename = basename($path);
			$dirname = dirname($path);
			$dir = $this->dir($dirname);
			return $dir->file($basename);
		}

		/**
		 * @param $name
		 * @return Directory
		 */
		public function newDir($name){
			if(!$this->isValidName($name)){
				throw new \LogicException();
			}
			return $this->_instantiateDirectory($name,false);
		}

		/**
		 * @param $name
		 * @param null $type
		 * @return File
		 */
		public function newFile($name, $type = null){
			if(!$this->isValidName($name)){
				throw new \LogicException();
			}
			return $this->_instantiateFile($name,false,$type);
		}



		/**
		 * @param $path
		 * @return Directory|File
		 */
		public function load($path){
			$adapter = $this->getAdapter();
			if($adapter->is_dir($path)){
				return $this->_instantiateDirectory($path , true);
			}
			if($adapter->is_file($path)){
				$type = null;
				/**
				 * Check type and Exists full absolute
				 */
				return $this->_instantiateFile($path , true , $type);
			}

			throw new \LogicException('Error loading node from absolute "'.$path.'"');

		}


		/**
		 * @param $name
		 * @param bool $nameIsReal
		 * @param $type
		 * @return File
		 */
		protected function _instantiateFile($name,$nameIsReal = false, $type = null){
			return new File($name,$nameIsReal,$this);
		}

		/**
		 * @param $name
		 * @param bool $nameIsReal
		 * @return Directory
		 */
		protected function _instantiateDirectory($name,$nameIsReal = false){
			return new Directory($name,$nameIsReal,$this);
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public static function isValidName($name){
			return (bool)preg_match('@^[^\\\\/:*?<>|]+$@',$name);
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public static function isValidPath($name){
			return (bool)preg_match('@^[^*?<>|]+$@',$name);
		}

	}
}

