<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.04.2016
 * Time: 23:52
 */
namespace Jungle\RegExp\Type {

	use Jungle\RegExp\Type;


	trait TypeAggregateTrait{

		/** @var  Type[]*/
		protected $types = [];

		/**
		 * @param array $types
		 * @return $this
		 */
		public function setTypes(array $types){
			$this->types = $types;
			return $this;
		}

		/**
		 * @param string|array $typeName
		 * @param string $pattern
		 * @param string $vartype
		 * @param callable|null $renderer
		 * @return Type
		 */
		public function add($typeName, $pattern = null, $vartype = 'string',callable $renderer = null){
			$type = new Type($typeName, $pattern, $vartype, $renderer);
			$this->types[] = $type;
			$this->afterCreate($type);
			return $type;
		}

		/**
		 * @param $typeName
		 * @param null $pattern
		 * @param string $vartype
		 * @param callable|null $renderer
		 * @return TypeContainer
		 */
		public function addContainer($typeName, $pattern = null, $vartype = 'string',callable $renderer = null){
			$type = new TypeContainer($typeName, $pattern, $vartype, $renderer);
			$this->types[] = $type;
			$this->afterCreate($type);
			return $type;
		}

		/**
		 * @param Type $type
		 * @param bool|true $checkExists
		 * @return $this
		 */
		public function addType(Type $type, $checkExists = true){
			if(!$checkExists || $this->searchType($type)===false){
				$this->types[] = $type;
			}
			return $this;
		}

		/**
		 * @param Type $type
		 * @return int|bool
		 */
		public function searchType(Type $type){
			return array_search($type,$this->types, true);
		}

		/**
		 * @param Type $type
		 * @return $this
		 */
		public function removeType(Type $type){
			$i = $this->searchType($type);
			if($i!== false){
				array_splice($this->types,$i,1);
			}
			return $this;
		}


		/**
		 * @param $name
		 * @return Type|null
		 */
		public function getType($name){
			if(!is_array($name)){
				if(strpos($name,'.')!==false){
					$name = explode('.',$name);
				}else{
					$name = [$name];
				}
			}
			$n = array_shift($name);
			foreach($this->types as $type){
				if($type->isName($n)){
					if($name){
						if(!$type instanceof TypeContainer){
							throw new \LogicException('Type "'.$n.'" is not a container type!');
						}
						return $type->getType($name);
					}else{
						return $type;
					}
				}
			}
			return null;
		}

		abstract protected function afterCreate(Type $type);



	}
}
