<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 19:00
 */
namespace Jungle\Data\Foundation\Schema\OuterInteraction {

	use Jungle\Data\Foundation\Record\Properties\PropertyRegistryInterface;
	use Jungle\Data\Foundation\Schema\OuterInteraction\ValueAccessor\Getter;
	use Jungle\Data\Foundation\Schema\OuterInteraction\ValueAccessor\GetterInterface;
	use Jungle\Data\Foundation\Schema\OuterInteraction\ValueAccessor\Setter;
	use Jungle\Data\Foundation\Schema\OuterInteraction\ValueAccessor\SetterInterface;

	/**
	 * Class ValueAccessor
	 * @package Jungle\Data\Foundation\Schema\OuterInteraction
	 */
	class ValueAccessor{

		/** @var  ValueAccessor */
		protected static $singletone;

		/** @var bool  */
		protected static $singletone_already_used = false;

		/** @var  mixed */
		protected $data;

		/** @var  ValueAccessAwareInterface */
		protected $accessor;



		/**
		 * @return ValueAccessor
		 */
		public static function useSingletone(){
			if(self::$singletone_already_used){
				throw new \LogicException('Instance already in use!');
			}
			if(!self::$singletone){
				self::$singletone = new ValueAccessor();
			}
			return self::$singletone;
		}

		/**
		 *
		 */
		public static function idleSingletone(){
			self::$singletone_already_used = false;
		}

		/**
		 * @param ValueAccessAwareInterface $accessor
		 * @return $this
		 */
		public function setAccessor(ValueAccessAwareInterface $accessor){
			$this->accessor = $accessor;
			return $this;
		}

		/**
		 * @return ValueAccessAwareInterface
		 */
		public function getAccessor(){
			return $this->accessor;
		}

		/**
		 * @param $data
		 * @return $this
		 */
		public function setData($data){
			$this->data = $data;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getData(){
			return $this->data;
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function __get($name){
			return $this->accessor->valueAccessGet($this->data, $name);
		}

		/**
		 * @param $name
		 * @param $value
		 * @return mixed
		 */
		public function __set($name,$value){
			$this->data = $this->accessor->valueAccessSet($this->data, $name,$value);
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function __isset($name){
			return $this->accessor->valueAccessExists($name);
		}









		/**
		 * @return SetterInterface
		 */
		public static function getDefaultSetter(){
			static $setter;
			if(!$setter){
				$setter = new Setter();
			}
			return $setter;
		}

		/**
		 * @return GetterInterface
		 */
		public static function getDefaultGetter(){
			static $getter;
			if(!$getter){
				$getter = new Getter();
			}
			return $getter;
		}

		/**
		 * @param $getter
		 * @return mixed
		 */
		public static function checkoutGetter($getter){
			$array = is_array($getter);
			if($getter instanceof GetterInterface || $getter instanceof \Closure || $array){
				if($array){
					/** @var array $getter */
					$getter = array_replace([
						'prefix' => null,
						'method' => null,
						'arguments' => []
					],$getter);
					if(!$getter['prefix'] && !$getter['method']){
						throw new \LogicException('Error object access getter');
					}
				}
				return $getter;
			}

			throw new \LogicException('Error invalid access getter');

		}

		/**
		 * @param $setter
		 * @return mixed
		 */
		public static function checkoutSetter($setter){
			$array = is_array($setter);
			if($setter instanceof SetterInterface || $setter instanceof \Closure || $array){
				if($array){
					/** @var array $setter */
					$setter = array_replace([
						'prefix'                    => null,
						'method'                    => null,
						'arguments'                 => [],
						'arguments_value_capture'   => null
					],$setter);
					if(!$setter['prefix'] && !$setter['method']){
						throw new \LogicException('Error object access getter');
					}
				}
				return $setter;
			}
			throw new \LogicException('Error invalid access setter');
		}

		/**
		 * @param $getter
		 * @param $data
		 * @param $key
		 * @param array $argumentsAhead
		 * @return mixed|null
		 */
		public static function handleGetter($getter, $data, $key, array $argumentsAhead = []){
			if(is_callable($getter)){
				$arguments = [$data, $key];
				if($argumentsAhead){
					$arguments = array_merge($arguments, $argumentsAhead);
				}
				return call_user_func_array($getter, $arguments);
			}elseif(is_array($getter)){
				$getter = array_replace([
					'prefix' => null,
					'method' => null,
					'arguments' => []
				],$getter);
				$methodName = null;
				if($getter['prefix']){
					$methodName =  $getter['prefix'] . ucfirst($key);
				}elseif($getter['method']){
					$methodName = $getter['method'];
				}
				$arguments = [];
				if($getter['arguments']){
					$arguments = $getter['arguments'];
				}
				if($methodName){
					return call_user_func_array([$data, $methodName], (array)$arguments);
				}else{
					throw new \LogicException('Object value access failure!');
				}
			}else{
				return null;
			}
		}

		public static function handleSetter($setter, $data, $key, $value,array $argumentsAhead = []){
			if(is_callable($setter)){
				$arguments =[$data, $key, $value];
				if($argumentsAhead){
					$arguments = array_merge($arguments, $argumentsAhead);
				}
				return call_user_func_array($setter, $arguments);
			}elseif(is_array($setter)){
				$setter = array_replace([
					'prefix'                    => null,
					'method'                    => null,
					'arguments'                 => [],
					'arguments_value_capture'   => null
				],$setter);

				$methodName = null;
				if($setter['prefix']){
					$methodName =  $setter['prefix'] . ucfirst($key);

				}elseif($setter['method']){
					$methodName = $setter['method'];
				}
				if($setter['arguments']){
					$arguments = $setter['arguments'];
					$valueCapture = $setter['arguments_value_capture'];

					if(is_string($valueCapture)){
						switch($valueCapture){
							case 'append':
								$arguments[] = $value;
								break;
							case 'prepend':
								array_unshift($arguments, $value);
								break;
						}
					}elseif(is_array($valueCapture)){
						if(!isset($valueCapture['type'])){
							throw new \LogicException('Value capture error array definition: invalid type parameter');
						}
						if(!isset($valueCapture['offset'])){
							throw new \LogicException('Value capture error array definition: invalid offset parameter');
						}
						$offset = $valueCapture['offset'];
						if($offset < 0 || $offset > count($arguments)){
							throw new \LogicException('Value capture error array definition: invalid offset parameter not range');
						}
						switch($valueCapture['type']){
							case 'insert':
								array_splice($arguments, $valueCapture['offset'], 0, [$value]);
								break;
							case 'replace':
								array_splice($arguments, $valueCapture['offset'], 1, [$value]);
								break;
						}
					}else{

					}
				}else{
					$arguments = [$value];
				}

				if($methodName){
					call_user_func_array([$data, $methodName], (array)$arguments);
					return $data;
				}else{
					throw new \LogicException('Object value access failure!');
				}
			}else{
				throw new \LogicException();
			}
		}


		/**
		 * @param ValueAccessAwareInterface|callable|null $access
		 * @param \Jungle\Data\Foundation\Record\Properties\PropertyRegistryInterface|mixed $data
		 * @param string $key
		 * @return mixed
		 */
		public static function handleAccessGet($access, $data, $key){
			if($data instanceof PropertyRegistryInterface){
				$value = $data->getProperty($key);
			}elseif($access instanceof ValueAccessAwareInterface){
				$value = $access->valueAccessGet($data,$key);
			}elseif(is_array($access) || is_callable($access)){
				$value = self::handleGetter($access, $data, $key);
			}else{
				throw new \LogicException();
			}
			return $value;
		}

		/**
		 * @param ValueAccessAwareInterface|callable|null $access
		 * @param \Jungle\Data\Foundation\Record\Properties\PropertyRegistryInterface|mixed $data
		 * @param string $key
		 * @param mixed $value
		 * @return \Jungle\Data\Foundation\Record\Properties\PropertyRegistryInterface|mixed
		 */
		public static function handleAccessSet($access, $data, $key, $value){
			if($data instanceof PropertyRegistryInterface){
				$data->setProperty($key, $value);
			}elseif($access instanceof ValueAccessAwareInterface){
				$data = $access->valueAccessSet($data,$key, $value);
			}elseif(is_array($access) || is_callable($access)){
				$data = self::handleSetter($access, $data, $key, $value);
			}else{
				throw new \LogicException();
			}
			return $data;
		}




	}
}

