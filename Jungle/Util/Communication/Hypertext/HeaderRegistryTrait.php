<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.10.2016
 * Time: 0:01
 */
namespace Jungle\Util\Communication\Hypertext {

	/**
	 * Class HeaderRegistry
	 * @package Jungle\Util\Communication\Hypertext
	 * @implements HeaderRegistryInterface
	 */
	trait HeaderRegistryTrait{

		/** @var array  */
		protected $headers = [];

		/**
		 * @param $key
		 * @param $value
		 * @param bool $reset
		 * @param bool $collection
		 * @return $this
		 */
		public function setHeader($key, $value, $reset = true, $collection = false){
			$key = Header::normalize($key);
			if($reset || !isset($this->headers[$key])){
				$this->headers[$key] = [];
			}
			if(is_array($value)){
				if($collection){
					foreach($value as $item){
						$this->headers[$key][] = is_array($item)?Header::renderHeaderValue($item):$item;
					}
				}else{
					$this->headers[$key][] = Header::renderHeaderValue($value);
				}
			}else{
				$this->headers[$key][] = $value;
			}
			return $this;
		}


		/**
		 * @param $key
		 * @param array $value
		 * @param bool $reset
		 * @return mixed
		 */
		public function mergeHeader($key, array $value, $reset = false){
			$key = Header::normalize($key);
			if($reset || !isset($this->headers[$key])){
				$this->headers[$key] = [];
			}
			$v = isset($this->headers[$key][0])?$this->headers[$key][0]:null;
			$v = Header::parseHeaderValue($v);

			if(isset($value['value'])){
				$v['value'] = $value['value'];
			}
			if(isset($value['params'])){
				$v['params'] = array_replace($v['params'], $value['params']);
			}
			if(isset($value['elements'])){
				$v['elements'] = $value['params'];
			}
			$this->headers[$key][0] = Header::renderHeaderValue($v);
			return $this;
		}



		/**
		 * @param $key
		 * @return array
		 */
		public function getHeaderCollection($key){
			if(isset($this->headers[$key])){
				return $this->headers[$key];
			}
			return [];
		}

		/**
		 * @param $key
		 * @param null $default
		 * @return mixed
		 */
		public function getHeader($key, $default = null){
			if(isset($this->headers[$key])){
				return isset($this->headers[$key][0])?$this->headers[$key][0]:$default;
			}
			return $default;
		}

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasHeader($key){
			return isset($this->headers[$key]);
		}

		/**
		 * @param $key
		 * @return int
		 */
		public function countHeader($key){
			return isset($this->headers[$key])?count($this->headers[$key]):0;
		}


		/**
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function appendHeader($key, $value){
			$key = Header::normalize($key);
			if(!isset($this->headers[$key])){
				$this->headers[$key] = [];
			}
			array_splice($this->headers[$key],count($this->headers[$key]),0,is_array($value)?$value:[$value]);
			return $this;
		}

		/**
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function prependHeader($key, $value){
			$key = Header::normalize($key);
			if(!isset($this->headers[$key])){
				$this->headers[$key] = [];
			}
			array_splice($this->headers[$key],0,0,is_array($value)?$value:[$value]);
			return $this;
		}

		/**
		 * @param $key
		 * @return $this
		 */
		public function removeHeader($key){
			unset($this->headers[$key]);
		}

		/**
		 * @param $key
		 * @param null $default
		 * @return mixed
		 */
		public function shiftHeader($key, $default = null){
			if(isset($this->headers[$key])){
				$value = array_shift($this->headers[$key]);
				if(!$this->headers[$key]){
					unset($this->headers[$key]);
				}
				return $value;
			}
			return $default;
		}

		/**
		 * @param $key
		 * @param null $default
		 * @return mixed
		 */
		public function popHeader($key, $default = null){
			if(isset($this->headers[$key])){
				$value = array_pop($this->headers[$key]);
				if(!$this->headers[$key]){
					unset($this->headers[$key]);
				}
				return $value;
			}
			return $default;
		}

		/**
		 * @param $key
		 * @param null $default
		 * @return mixed
		 */
		public function getHeaderFirst($key, $default = null){
			return isset($this->headers[$key])?array_slice($this->headers[$key],0,1)[0]:$default;
		}

		/**
		 * @param $key
		 * @param null $default
		 * @return mixed
		 */
		public function getHeaderLast($key, $default = null){
			return isset($this->headers[$key])?array_slice($this->headers[$key],-1,1)[0]:$default;
		}






		/**
		 * @param array $headers
		 * @param bool|false $merge
		 * @param bool|false $pairs
		 * @return $this
		 */
		public function setHeaders(array $headers, $merge = false, $pairs = false){
			if($merge){
				if($pairs){
					$_h = [];
					foreach($headers as list($key,$value)){
						$key = Header::normalize($key);
						if(!isset($_h[$key])) $_h[$key] = [];
						$_h[$key][] = $value;
					}
					$this->headers = array_replace($this->headers,$_h);
				}else{
					$_h = [];
					foreach($headers as $key => $value){
						$key = Header::normalize($key);
						if(is_array($value)){
							$_h[$key] = $value;
						}else{
							if(!isset($_h[$key]))$_h[$key] = [];
							$_h[$key][] = $value;
						}
					}
					$this->headers = array_replace($this->headers,$_h);
				}
			}else{
				if($pairs){
					$this->headers = [];
					foreach($headers as list($key,$value)){
						$key = Header::normalize($key);
						if(!isset($this->headers[$key])) $this->headers[$key] = [];
						$this->headers[$key][] = $value;
					}
				}else{
					$_h = [];
					foreach($headers as $key => $value){
						$key = Header::normalize($key);
						if(is_array($value)){
							$_h[$key] = $value;
						}else{
							if(!isset($_h[$key]))$_h[$key] = [];
							$_h[$key][] = $value;
						}
					}
					$this->headers = $_h;
				}
			}
			return $this;

		}

		/**
		 * @return array
		 */
		public function getHeaders(){
			return $this->headers;
		}

		/**
		 * @return array
		 */
		public function getHeaderPairs(){
			$a = [];
			foreach($this->headers as $key => $collection){
				foreach($collection as $value){
					$a[] = [$key, $value];
				}
			}
			return $a;
		}




		/**
		 * @param $key
		 * @param $value
		 * @param $inCollection
		 * @return bool
		 */
		public function checkHeader($key, $value, $inCollection = false){
			if(isset($this->headers[$key])){
				if($inCollection){
					foreach($this->headers[$key] as $v){
						if(strcasecmp($v,$value)===0){
							return true;
						}
					}
				}else{
					return strcasecmp($this->headers[$key][0],$value)===0;
				}
			}
			return false;
		}

		/**
		 * @param $key
		 * @param $value
		 * @param $inCollection
		 * @return bool
		 */
		public function haveHeader($key, $value, $inCollection = false){
			if(isset($this->headers[$key])){
				if($inCollection){
					foreach($this->headers[$key] as $v){
						if(stripos($v,$value)!==false){
							return true;
						}
					}
					return in_array($value,$this->headers[$key]);
				}else{
					return stripos($this->headers[$key][0],$value)!==false;
				}
			}
			return false;
		}


	}
}

