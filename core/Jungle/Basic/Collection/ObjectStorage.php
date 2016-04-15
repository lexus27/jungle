<?php
/**
 * Created by PhpStorm.
 * Project: jungle
 * Date: 12.04.2015
 * Time: 14:20
 */

namespace Jungle\Basic\Collection;

/**
 * Class ObjectStorage
 * @package Jungle\Basic\Collection
 */
class ObjectStorage implements \ArrayAccess, \Countable, \Iterator{

	protected $_i = 0;

	protected $objects = [];
	protected $data = [];
	protected $count = 0;


	/**
	 * @return array
	 */
	public function getAllObjects(){
		return $this->objects;
	}

	/**
	 * @return array
	 */
	public function getAllData(){
		return $this->data;
	}

	/**
	 *
	 */
	public function clear(){
		$this->count = 0;
		$this->data = [];
		$this->objects = [];
	}

	/**
	 * @param object $object
	 * @param mixed $value
	 */
	public function set($object,$value){
		$i = $this->search($object);
		if($i===false){
			$i = $this->count++;
			$this->objects[$i] = $object;
		}
		$this->data[$i] = $value;
	}

	/**
	 * @param $object
	 * @param $value
	 */
	public function setLink($object, & $value){
		$i = $this->search($object);if($i===false){$i = $this->count++;$this->objects[$i] = $object;}$this->data[$i] = & $value;
	}

	/**
	 * @param object $object
	 * @return mixed
	 */
	public function & get($object){
		$a = null;
		if(($i = $this->search($object))!==false){
			$a = & $this->data[$i];
		}
		return $a;
	}

	/**
	 * @param object $object
	 * @return bool
	 */
	public function has($object){
		return $this->search($object)!==false;
	}

	/**
	 * @param object $object
	 */
	public function remove($object){
		if(($i = $this->search($object))!==false){
			array_splice($this->objects,$i,1);
			array_splice($this->data,$i,1);
			$this->count--;
		}
	}

	/**
	 * @param $object
	 * @return mixed
	 */
	protected function search($object){
		return array_search($object,$this->objects,true);
	}


	/**
	 * @param object $object
	 * @return bool
	 */
	public function offsetExists($object){
		return $this->has($object);
	}

	/**
	 * @param object $object
	 * @return mixed
	 */
	public function & offsetGet($object){
		return $this->get($object);
	}

	/**
	 * @param object $object
	 * @param mixed $value
	 */
	public function offsetSet($object, $value){
		$this->set($object,$value);
	}

	/**
	 * @param mixed $object
	 */
	public function offsetUnset($object){
		$this->remove($object);
	}

	/**
	 * @param callable $sorter
	 * @return $this
	 */
	public function sortBy(callable $sorter){
		usort($this->objects,$sorter);
		return $this;
	}

	/**
	 * @param callable $sorter
	 * @return $this
	 */
	public function sortByData(callable $sorter){
		usort($this->data,$sorter);
		return $this;
	}



	/**
	 * @return int
	 */
	public function count(){
		return $this->count;
	}

	/**
	 * @return mixed
	 */
	public function & current(){
		return $this->objects[$this->_i];
	}

	/**
	 *
	 */
	public function next(){
		$this->_i++;
	}

	/**
	 * @return int
	 */
	public function key(){
		return $this->_i;
	}

	/**
	 * @return bool
	 */
	public function valid(){
		return isset($this->objects[$this->_i]);
	}

	/**
	 *
	 */
	public function rewind(){
		$this->_i = 0;
	}
}