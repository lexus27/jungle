<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 12.09.2015
 * Time: 15:29
 */
namespace Jungle\Basic\Collection {

	use Traversable;


	/**
	 * Class CollectionEach
	 * @package Jungle\Basic\Collection
	 * TODO доделать коллекцию
	 */
	class CollectionEach implements \ArrayAccess, \IteratorAggregate{

		const TYPE_OBJECT = 0;
		const TYPE_ARRAY = 1;

		/**
		 * @var array
		 */
		protected $collection = [];

		/**
		 * @var int
		 */
		protected $type;

		/**
		 * @param array $collection
		 */
		public function __construct(array $collection){
			foreach($collection as $element){
				if($this->type === null){
					if(is_object($element)){
						$this->type = self::TYPE_OBJECT;
					}else if(is_array($element)){
						$this->type = self::TYPE_ARRAY;
					}else{
						throw new \LogicException('Illegal type collection');
					}
				}else if($this->type === self::TYPE_ARRAY){
					if(!is_array($element)){
						throw new \LogicException('Неравномерные элементы коллекции');
					}
				}else if($this->type === self::TYPE_OBJECT){
					if(!is_object($element)){
						throw new \LogicException('Неравномерные элементы коллекции');
					}
				}
			}
		}

		public function isArray(){
			return $this->type === self::TYPE_ARRAY;
		}

		public function isObject(){
			return $this->type === self::TYPE_OBJECT;
		}


		/**
		 * @param $method
		 * @param $arguments
		 * @return $this|\Generator|null
		 */
		public function & __call($method, $arguments){
			$collected = [];
			if($this->isObject()){
				$returnSelf = true;
				foreach($this->collection as $item){
					if(method_exists($item,$method)){
						$collected[] = $result = call_user_func_array([$item,$method],$arguments);
						if($result !== $item){
							$returnSelf = false;
						}
					}else{
						throw new \LogicException('Method not exists in item collectionEach');
					}
				}

				if($returnSelf){
					return $this;
				}else{
					return $this->eachResults($collected);
				}
			}
			return null;
		}

		/**
		 * @param array $collected
		 * @return \Generator
		 * USE list($item,$returnedValue)
		 */
		protected function eachResults(array $collected){
			if(count($collected)!== count($this->collection)){
				return;
			}
			foreach($collected as $i => $value){
				yield $i => [$this->collection[$i],$value];
			}
		}


		/**
		 * @param $property
		 * @return \Generator|null
		 */
		public function & __get($property){
			if($this->isObject()){
				$collected = [];
				foreach($this->collection as $item){
					if(isset($item->{$property})){
						$collected[] = $item->{$property};
					}else{
						throw new \LogicException('Not have property');
					}
				}
				return $this->eachResults($collected);
			}
			return null;
		}

		/**
		 * @param $property
		 * @param $value
		 * @return null
		 */
		public function & __set($property, $value){
			if($this->isObject()){
				foreach($this->collection as $item){
					if(isset($item->{$property})){
						$item->{$property} = $value;
					}else{
						throw new \LogicException('Not have property');
					}
				}
			}
			return null;
		}




		public function offsetExists($offset){
			if($this->isArray()){

			}else if($this->isObject()){

			}
		}

		public function offsetGet($offset){
			if($this->isArray()){

			}else if($this->isObject()){

			}
		}

		public function offsetSet($offset, $value){
			if($this->isArray()){

			}
		}

		public function offsetUnset($offset){
			if($this->isArray()){

			}else if($this->isObject()){

			}
		}

		/**
		 * @return \Generator
		 */
		public function getIterator(){
			foreach($this->collection as $i => $item){
				yield $i => $item;
			}
		}
	}
}

