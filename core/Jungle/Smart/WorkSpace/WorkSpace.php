<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 05.05.2015
 * Time: 1:43
 */

namespace Jungle\Smart\WorkSpace {

	use Jungle\Basic\INamed;

	/**
	 * Class NaSpace
	 * @package Jungle\Smart\NaSpace
	 */
	class WorkSpace implements INamed{

		const DELIMITER = '.';

		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var WorkSpace
		 */
		protected $parent;

		/**
		 * @var WorkSpace[]
		 */
		protected $children = [];

		/**
		 * @var \ArrayAccess
		 */
		protected $container;

		/**
		 * @param string $name
		 * @return $this
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}


		/**
		 * @return WorkSpace
		 */
		public function getDefault(){
			if(!$this->parent){
				return $this;
			}else{
				return $this->parent->getDefault();
			}
		}

		/**
		 * @return bool
		 */
		public function isDefault(){
			return !$this->parent;
		}

		/**
		 * @param WorkSpace $space
		 * @return bool
		 */
		public function isContains(WorkSpace $space){
			if($this === $space){
				return true;
			}else{
				foreach($this->children as $children){
					if($children->isContains($space)){
						return true;
					}
				}
			}
			return false;
		}

		/**
		 * @param WorkSpace $parent
		 * @param bool $addNew
		 * @param bool $removeOld
		 * @return $this
		 */
		public function setParent(WorkSpace $parent = null, $addNew = true, $removeOld = true){
			if($parent && $this->isContains($parent)){
				throw new \LogicException('Parent error, passed workspace for parent is contains in this workspace');
			}
			$old = $this->parent;
			if($old !== $parent){
				$this->parent = $parent;

				if($parent && $addNew)$parent->addChildren($this,false);
				if($old && $removeOld)$old->removeChildren($this,false);
			}
			return $this;
		}

		/**
		 * @return WorkSpace
		 */
		public function getParent(){
			return $this->parent;
		}


		/**
		 * @param WorkSpace $workSpace
		 * @param bool $setParent
		 * @return $this
		 */
		public function addChildren(WorkSpace $workSpace, $setParent = true){
			$i = $this->searchChildren($workSpace);
			if($i === false){
				$this->children[] = $workSpace;
				if($setParent) $workSpace->setParent($this, false);
			}
			return $this;
		}

		/**
		 * @param WorkSpace $workSpace
		 * @return mixed
		 */
		public function searchChildren(WorkSpace $workSpace){
			return array_search($workSpace, $this->children, true);
		}

		/**
		 * @param WorkSpace $workSpace
		 * @param bool $setParent
		 * @return $this
		 */
		public function removeChildren(WorkSpace $workSpace, $setParent = true){
			$i = $this->searchChildren($workSpace);
			if($i !== false){
				array_splice($this->children, $i, 1);
				if($setParent) $workSpace->setParent(null, false, false);
			}
			return $this;
		}

		/**
		 * @param $workspaceName
		 * @return WorkSpace|null
		 */
		public function find($workspaceName){
			foreach($this->children as $child){
				if(strcasecmp($child->getName(),$workspaceName) === 0){
					return $child;
				}
			}
			return null;
		}

		/**
		 * @param \ArrayAccess|array $container
		 * @return $this
		 */
		public function setContainer($container){
			if(!is_array($container) || !($container instanceof \ArrayAccess)){
				throw new \InvalidArgumentException('Container must be array or \ArrayAccess');
			}
			$this->container = $container;
			return $this;
		}

		/**
		 * @param $query
		 * @param callable $handler
		 * @return \ArrayAccess|mixed|null
		 * @internal param $isset
		 */
		protected function _query($query, callable $handler){
			if(!is_array($query)) $query = explode(self::DELIMITER, $query);
			$container = null;
			$space = $this;
			do{
				$item = array_shift($query);
				if($space){
					$_c = $space;
					$space = $space->find($item);
					if(!$space && $_c->container){
						$container = $_c->container;
						$space = null;
						if(!count($query)){
							return call_user_func($handler,$container,$item);
						}
					}
				}elseif($container instanceof \ArrayAccess){
					if(!count($query)){
						return call_user_func($handler,$container,$item);
					}else{
						$container = $container->offsetGet($item);
						if(!is_array($container) && !$container instanceof \ArrayAccess){
							$container = null;
							return call_user_func($handler,$container,$item);
						}
					}

				}elseif(is_array($container)){
					if(!count($query)){
						return call_user_func($handler,$container,$item);
					}else{
						$container = $container[$item];
						if(!is_array($container) && !$container instanceof \ArrayAccess){
							$container = null;
							return call_user_func($handler,$container,$item);
						}
					}
				}
			}while($query);
			return call_user_func($handler,($space?$space->container:$container),null);
		}

		/**
		 * @param $query
		 * @return mixed
		 */
		public function queryGet($query){
			static $fn = null;
			if(!$fn) $fn = function(\ArrayAccess $container = null,$key){
				return $container && $key && isset($container[$key])?$container[$key]:null;
			};
			return $this->_query($query,$fn);
		}

		/**
		 * @param $query
		 * @return bool
		 */
		public function queryIsset($query){
			static $fn = null;
			if(!$fn) $fn = function($container = null,$key){
				return $container && $key && isset($container[$key]);
			};
			return $this->_query($query,$fn);
		}

		/**
		 * @param $query
		 * @param $value
		 * @return bool
		 */
		public function querySet($query,$value){
			return $this->_query($query,function($container = null,$key) use ($value){
				return $container && $key?$container[$key] = $value:false;
			});
		}


		/**
		 * @param $query
		 * @return bool
		 */
		public function queryRemove($query){
			static $fn = null;
			if(!$fn) $fn = function($container = null,$key){
				if($container && $key){
					unset($container[$key]);
				}
				return true;
			};
			return $this->_query($query,$fn);
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return $this->getNamespaceString();
		}

		/**
		 * @return string
		 */
		public function getNamespaceString(){
			return ($this->parent ? $this->parent . self::DELIMITER : '') . $this->getName();
		}

	}
}