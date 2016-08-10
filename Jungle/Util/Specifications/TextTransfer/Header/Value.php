<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 09.01.2016
 * Time: 22:33
 */
namespace Jungle\Util\Specifications\TextTransfer\Header {

	use Jungle\Util\Smart\Value\IValue;
	use Jungle\Util\Smart\Value\IValueSettable;

	/**
	 * Class HeaderValue
	 * @package Jungle\HeaderCover
	 */
	class Value implements IValue, IValueSettable{

		/**
		 * @var string
		 */
		protected $value;

		/**
		 * @var string[]
		 */
		protected $params = [];

		/**
		 * @var string[]
		 */
		protected $elements = [];

		/**
		 * @param $value
		 * @return $this
		 */
		public function setValue($value){
			if($this->value !== $value){
				$this->value = $value;
			}
			return $this;
		}

		/**
		 *
		 */
		public function getValue(){
			return $this->value;
		}

		/**
		 * @param IValue|mixed $v
		 * @return bool
		 */
		public function equal($v){
			if(is_string($v)){
				$v = $this->recognize($v);
			}elseif($v instanceof self){
				$v = $v->toArray();
			}
			$v = (array)$v;
			$a = $this->toArray();
			return  (isset($v['value']) && isset($v['params']) && isset($v['elements'])) &&
					$a['value'] === $v['value'] &&
		            $a['params'] === $v['params'] &&
		            $a['elements'] === $v['elements'];
		}

		/**
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function setParam($key, $value){
			if(!isset($this->params[$key]) || $this->params[$key]!==$value){
				$this->params[$key] = $value;
			}
			return $this;
		}

		/**
		 * @param $key
		 * @return null|string
		 */
		public function getParam($key){
			return isset($this->params[$key])?$this->params[$key]:null;
		}

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasParam($key){
			return isset($this->params[$key]);
		}

		/**
		 * @param $key
		 * @return $this
		 */
		public function removeParam($key){
			if(isset($this->params[$key])){
				unset($this->params[$key]);
			}
			return $this;
		}

		/**
		 * @return array
		 */
		public function getElements(){
			return $this->elements;
		}

		/**
		 * @param $flag
		 * @return $this
		 */
		public function addElement($flag){
			if(($i = $this->searchElement($flag))===false){
				$this->elements[] = $flag;
			}
			return $this;
		}

		/**
		 * @param $flag
		 * @return bool|int
		 */
		public function searchElement($flag){
			return array_search($flag,$this->elements,true);
		}

		/**
		 * @param $flag
		 * @return $this
		 */
		public function removeElement($flag){
			if(($i = $this->searchElement($flag))!==false){
				array_splice($this->elements,$i,1);
			}
			return $this;
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return $this->composite($this->toArray());
		}

		/**
		 * @param $chunk
		 * @param bool|mixed $element
		 * @return array|bool
		 */
		public function decompositeValueChunk($chunk,& $element=true){
			$chunk = trim($chunk);
			if(preg_match('@^([\w]+)=(.+)@',$chunk,$m)){
				$key = trim($m[1]);
				$value = trim(trim($m[2]),'"\'');
				$element = false;
				return [$key,$value];
			}else{
				$element = $chunk;
				return $element;
			}
		}

		/**
		 * @param $value
		 * @return mixed
		 */
		public static function normalize($value){
			$value = preg_replace('@;[\r\n\t\s]+@','; ',trim($value));
			return $value;
		}

		/**
		 * @param $value
		 * @return null|array value, params, options
		 */
		public function recognize($value){
			$value = preg_split('@;[\r\n\t\s]+@',$this->normalize($value));
			if(isset($value[0])){
				$params = [];
				$elements = [];
				$main = array_shift($value);
				$main = $this->decompositeValueChunk($main,$element);
				if(!$element){
					$params[$main[0]] = $main[1];
					$main = null;
				}
				foreach($value as $pair){
					$pair = $this->decompositeValueChunk($pair,$element);
					if($element){
						$elements[] = $element;
					}else{
						$params[$pair[0]] = $pair[1];
					}
				}
				return [
					'value'     => $main,
					'params'    => $params,
					'elements'   => $elements
				];
			}
			return null;
		}


		/**
		 * @param array $a
		 * @return string
		 */
		public function composite(array $a=null){
			if($a===null)$a = $this->toArray();
			$value      = isset($a['value'])?$a['value']:null;
			$params     = isset($a['params'])?$a['params']:[];
			$elements   = isset($a['elements'])?$a['elements']:[];
			$p = null;
			if($params){
				$p = [];
				foreach($params as $k => $v){
					$p[] = $k.'="'.$v.'"';
				}
			}
			return $value.($p?'; '.implode('; ',$p):'').($elements?'; '.implode('; ',$elements):'');
		}

		/**
		 * @return array
		 */
		public function toArray(){
			return [
				'value'     => $this->value,
				'params'    => $this->params,
				'elements'  => $this->elements
			];
		}

		/**
		 * @param array $arrayRepresent
		 * @return $this
		 */
		public function fromArray(array $arrayRepresent){
			$this->value = (isset($arrayRepresent['value'])?$arrayRepresent['value']:null);
			$this->params = (isset($arrayRepresent['params'])?(array)$arrayRepresent['params']:[]);
			$this->elements = (isset($arrayRepresent['elements'])?(array)$arrayRepresent['elements']:[]);
			return $this;
		}




	}
}

