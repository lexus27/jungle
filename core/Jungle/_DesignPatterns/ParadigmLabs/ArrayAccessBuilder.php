<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 15.11.2015
 * Time: 1:39
 */
namespace Jungle\_DesignPatterns\ParadigmLabs {

	/**
	 * Class ArrayAccessBuilder
	 * @package Jungle\_DesignPatterns\ParadigmLabs
	 */
	class ArrayAccessBuilder implements \ArrayAccess{

		/** @var INamed[] */
		protected $collection = [];

		/**
		 * @param mixed $alias
		 * @return bool
		 *
		 * @impl Simple getItemByName method
		 */
		public function offsetExists($alias){
			foreach($this->collection as $item){
				if($item->getName()===$alias){
					return true;
				}
			}
			return false;
		}

		/**
		 * @param mixed $alias
		 * @return INamed|null
		 */
		public function offsetGet($alias){
			foreach($this->collection as $item){
				if($item->getName()===$alias){
					return $item;
				}
			}
			return null;
		}

		/**
		 * @param mixed $alias
		 * @param mixed $definition
		 *
		 * @throws \Exception already exists checking fail
		 *
		 * @impl BUILDER METHOD(New) $alias use to "setName($alias)" $value use as object definition for factory
		 */
		public function offsetSet($alias, $definition){

			// check valid $alias
			if(!$alias){
				throw new \Exception('is not allowed append! please pass $index');
			}

			// check already exists
			if($this->offsetGet($alias)){
				//Error already exists
				throw new \Exception('Item by alias "'.$alias.'" already exists');
			}

			// instantiate
			$item = new BaseNamed();
			$item->setName($alias);

			// building
			if(!$definition){
				// default definition apply to $item or throw error
			}else{
				//  build $item from $definition
			}

			//add complete build to collection
			$this->collection[] = $item;
		}

		/**
		 * @param mixed $alias
		 */
		public function offsetUnset($alias){
			if( ( $o = $this->offsetGet($alias) ) ){
				$this->removeItem($o);
			}
		}

		/**
		 * @param INamed $item
		 * @return $this
		 * @throws \Exception
		 * @impl Collection EQUAL owner (has 1 or 2 way reference)
		 */
		public function addItem(INamed $item){
			if($this->searchItem($item)===false){
				$name = $item->getName();
				if($this[$name]){
					throw new \Exception('AddItem error: Already exists!');
				}
				$this->collection[] = $item;
			}
			return $this;
		}

		/**
		 * @param INamed $item
		 * @return mixed
		 * @impl Collection EQUAL owner (has 1 or 2 way reference)
		 */
		public function searchItem(INamed $item){
			return array_search($item,$this->collection,true);
		}

		/**
		 * @param INamed $item
		 * @return $this
		 *
		 * @impl Collection EQUAL owner (has 1 or 2 way reference)
		 */
		public function removeItem(INamed $item){
			if( ($i = $this->searchItem($item))!==false){
				array_splice($this->collection,$i,1);
				// after $this->collection clean code
			}
			return $this;
		}
	}
}

