<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 10.05.2015
 * Time: 21:26
 */

namespace Jungle\XPlate\CSS {


	use Jungle\Basic\INamedBase;

	/**
	 * Class Keyframe
	 * @package Jungle\XPlate\CSS
	 */
	class Animation implements INamedBase, \ArrayAccess, \Countable, \Traversable, \Iterator{

		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var Definition[]
		 */
		protected $definitions = [];

		/**
		 * @var bool
		 */
		protected $ordered = true;


		/**
		 * @return mixed
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			if(!$name) throw new \InvalidArgumentException('Name is empty');
			$this->name = $name;
			return $this;
		}


		/**
		 * @param int $position percentage
		 * @param Definition $definition
		 */
		public function setDefinition($position, Definition $definition){
			$this->definitions[$position] = $definition;
			$this->ordered = false;

		}

		/**
		 * @param $position
		 * @return Definition|null
		 */
		public function getDefinition($position){
			return isset($this->definitions[$position]) ? $this->definitions[$position] : null;
		}

		/**
		 * @param $position
		 * @return bool
		 */
		public function hasDefinition($position){
			return isset($this->definitions[$position]);
		}

		/**
		 * @param $position
		 */
		public function removeDefinition($position){
			unset($this->definitions[$position]);
		}

		/**
		 * @return Definition[]
		 * {percent} => Definition
		 */
		public function getDefinitions(){
			return $this->definitions;
		}


		/**
		 * @return Definition
		 */
		public function current(){
			return current($this->definitions);
		}

		/**
		 *
		 */
		public function next(){
			next($this->definitions);
		}

		/**
		 * @return int
		 */
		public function key(){
			return key($this->definitions);
		}

		/**
		 * @return bool
		 */
		public function valid(){
			return isset($this->definitions[key($this->definitions)]);
		}

		/**
		 *
		 */
		public function rewind(){
			if(!$this->ordered){
				ksort($this->definitions);
				$this->ordered = true;
			}
			reset($this->definitions);
		}


		/**
		 * @param mixed $offset
		 * @return bool
		 */
		public function offsetExists($offset){
			return $this->hasDefinition($offset);
		}

		/**
		 * @param mixed $offset
		 * @return Definition|null
		 */
		public function offsetGet($offset){
			return $this->getDefinition($offset);
		}

		/**
		 * @param mixed $offset
		 * @param mixed $value
		 */
		public function offsetSet($offset, $value){
			$this->setDefinition($offset, $value);
		}

		/**
		 * @param mixed $offset
		 */
		public function offsetUnset($offset){
			$this->removeDefinition($offset);
		}


		/**
		 * @return int
		 */
		public function count(){
			return count($this->definitions);
		}

	}
}