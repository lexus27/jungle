<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 09.01.2016
 * Time: 22:33
 */
namespace Jungle\Util\Specifications\Hypertext {

	use Jungle\Util\Smart\Keyword\Keyword;
	use Jungle\Util\Specifications\Hypertext\Header\Value;

	/**
	 * Class Header
	 * @package Jungle\HeaderCover
	 */
	class Header extends Keyword{

		const DEFAULT_PRIORITY_ENCODE = 1000;
		const DEFAULT_PRIORITY_DECODE = 1000;

		/** @var int */
		protected $priority_encode = self::DEFAULT_PRIORITY_ENCODE;

		/** @var int */
		protected $priority_decode = self::DEFAULT_PRIORITY_DECODE;

		/**
		 * @param string $identifier
		 */
		public function setIdentifier($identifier){
			parent::setIdentifier($this->normalize($identifier));
		}

		/**
		 * @param string $name
		 * @return string
		 */
		public static function normalize($name){
			$name = preg_replace('@[\-_ ]+@',' ',$name);
			$name = str_replace(' ','-',ucwords($name));
			return $name;
		}

		/**
		 * @param $header
		 * @return bool|string
		 */
		public static function parseHeaderRow($header){
			if(preg_match('@^(?:(\w+[\w\-]+\w+):)?(.*)@',$header,$m)){
				return [self::normalize($m[1]),Value::normalize($m[2])];
			}
			return false;
		}


		/**
		 * @param Value[] $values
		 * @param $contents
		 * @param HeaderRegistryInterface $headers
		 * @return null|string
		 */
		public function encodeContents(array $values, $contents, HeaderRegistryInterface $headers){}

		/**
		 * @param Value[] $values
		 * @param $contents
		 * @param HeaderRegistryInterface $headers
		 * @return null|string
		 */
		public function decodeContents(array $values, $contents, HeaderRegistryInterface $headers){}

		/**
		 * @return int
		 */
		public function getPriorityEncode(){
			return $this->priority_encode;
		}

		/**
		 * @return int
		 */
		public function getPriorityDecode(){
			return $this->priority_decode;
		}


		/**
		 * @param $value_raw
		 * @return array|null
		 */
		public static function parseHeaderValue($value_raw){
			$a = [
				'value' => null,
				'params' => [],
				'elements' => []
			];
			if(!$value_raw) return $a;

			$value_raw = explode("; ",$value_raw);
			foreach($value_raw as $i => $item){
				if($i <= 0){
					$a['value'] = $item;
				}else{
					$c = explode('=', $item, 2);
					if(count($c)>1){
						$a['params'][$c[0]] = trim($c[1],"'\"");
					}else{
						$a['elements'][] = $c[0];
					}
				}
			}
			return $a;
		}

		/**
		 * @param array $value
		 * @return string
		 */
		public static function renderHeaderValue(array $value){
			$a = [];
			if(isset($value['value'])){
				$a[] = $value['value'];
			}elseif(isset($value[0])){
				$a[] = $value[0];
			}
			if(isset($value['params']) && is_array($value['params']) && !empty($value['params'])){
				$c = [];
				foreach($value['params'] as $k => $v ){
					$c[] = $k . '=' . $v;
				}
				$a[] = implode('; ', $c);
			}
			if(isset($value['elements']) && is_array($value['elements']) && !empty($value['elements'])){
				$a[] = implode('; ', $value['elements']);
			}
			return implode('; ', $a);
		}



	}



}

