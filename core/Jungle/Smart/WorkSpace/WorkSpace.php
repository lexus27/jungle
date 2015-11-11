<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 05.05.2015
 * Time: 1:43
 */

namespace Jungle\Smart\WorkSpace {

	use Jungle\Basic\INamedBase;

	/**
	 * Class NaSpace
	 * @package Jungle\Smart\NaSpace
	 */
	class WorkSpace implements INamedBase{

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
		 * @param \ArrayAccess $container
		 * @return $this
		 */
		public function setContainer(\ArrayAccess $container){
			$this->container = $container;
			return $this;
		}

		/**
		 * @param $query
		 * @return mixed
		 */
		public function queryGet($query){
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
							return $container->offsetGet($item);
						}
					}
				}elseif($container instanceof \ArrayAccess){
					if(!count($query)){
						return $container->offsetGet($item);
					}else{
						$container = $container->offsetGet($item);
						if(!$container instanceof \ArrayAccess){
							$container = null;
							return null;
						}
					}

				}
			}while($query);
			return $space?$space->container:$container;
		}

		/**
		 * @param $query
		 * @return bool
		 */
		public function queryIsset($query){
			if(!is_array($query)) $query = explode(self::DELIMITER, $query);
			$container = null;
			$space = $this;
			do{
				$item = array_shift($query);
				if($container instanceof \ArrayAccess){
					if(!count($query)){
						return $container->offsetExists($item);
					}else{
						$container = $container->offsetGet($item);
						if(!$container instanceof \ArrayAccess){
							$container = null;
							return false;
						}
					}

				}elseif($space){
					$_c = $space;
					$space = $space->find($item);
					if(!$space && $_c->container){
						$container = $_c->container;
						$space = null;
						if(!count($query)){
							return $container->offsetExists($item);
						}
					}
				}
			}while($query);
			return (boolean) ( $space ? $space->container : $container );
		}

		/**
		 * @param $query
		 * @param $value
		 * @return bool
		 */
		public function querySet($query,$value){
			if(!is_array($query)) $query = explode(self::DELIMITER, $query);
			$container = null;
			$space = $this;
			do{
				$item = array_shift($query);
				if($container instanceof \ArrayAccess){
					if(!count($query)){
						$container->offsetSet($item, $value);
						return true;
					}else{
						$container = $container->offsetGet($item);
						if(!$container instanceof \ArrayAccess){
							$container = null;
							return false;
						}
					}

				}elseif($space){
					$_c = $space;
					$space = $space->find($item);
					if(!$space && $_c->container){
						$container = $_c->container;
						$space = null;
						if(!count($query)){
							return $container->offsetSet($item, $value);
						}
					}
				}
			}while($query);
			return false;
		}


		/**
		 * @param $query
		 * @return bool
		 */
		public function queryRemove($query){
			if(!is_array($query)) $query = explode(self::DELIMITER, $query);
			$container = null;
			$space = $this;
			do{
				$item = array_shift($query);
				if($container instanceof \ArrayAccess){
					if(!count($query)){
						$container->offsetUnset($item);
						return true;
					}else{
						$container = $container->offsetGet($item);
						if(!$container instanceof \ArrayAccess){
							$container = null;
							return false;
						}
					}

				}elseif($space){
					$_c = $space;
					$space = $space->find($item);
					if(!$space && $_c->container){
						$container = $_c->container;
						$space = null;
						if(!count($query)){
							$container->offsetUnset($item);
							return true;
						}
					}
				}
			}while($query);
			return false;
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