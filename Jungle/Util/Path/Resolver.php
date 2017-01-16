<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.01.2017
 * Time: 23:08
 */
namespace Jungle\Util\Path {

	/**
	 * Class Resolver
	 * @package Jungle\Util\Path
	 */
	class Resolver{



		/**
		 * @param $container
		 * @param $path
		 * @param array $options
		 */
		public function resolve($container, $path, array $options = [ ]){
			$pos = strpos($path,'.');
			if($pos!==false){

				$property = substr($path,0,$pos);



			}
		}

		/**
		 * @param $definition
		 */
		public function parseMethod($definition){

		}

		public function parseArray($definition){

		}

		public function tokenize($path){
			$length = strlen($path);
			$modes = [];
			for($i=0;$i<$length;$i++){
				$char = $path{$i};
				if($char === '.' && !in_array($modes,['string'])){
					$token = substr($path,0,$i);
					return [$token, substr($path,$i)?:null];
				}elseif($char === '['){
					$modes[$i] = 'array';
				}elseif($char === ']'){
					list($start_pos, $mode) = array_replace([null,null],each(array_slice($modes,-1,1)));
					if($mode === 'array'){
						$token = substr($path, $start_pos + 1, $i - $start_pos );
						return [$token, substr($path,$i)?:null];
					}

				}elseif($char === '('){
					$modes[$i] = 'function';
				}elseif($char === ')'){
					$mode = current(array_slice($modes,-1,1));



				}elseif(in_array($char,['\'','"'], true)){
					$modes[$i] = 'string';
				}
			}

		}


		function read_after($string, $position, $len = 1,$offset = 0){
			return substr($string,$position+$offset,$len);
		}
		function read_before($string, $position, $len = 1,$offset = 0){
			$pos  = $position - $offset;
			$start = $pos-$len;
			if($start < 0){
				$len+=$start;
				if(!$len)return '';
				$start = 0;
			}
			return substr($string,$start,$len);
		}
		function has_before($string, $position, $needle, $offset=0){
			if(!is_array($needle)){
				$needle = [$needle];
			}
			$ll = null;
			foreach($needle as $item){
				$l = strlen($item);
				if(!isset($s) || $ll != $l){
					$s = read_before($string,$position,$l,$offset);
					$ll = $l;
				}
				if($s === $item) return true;
			}
			return false;
		}
		function has_after($string, $position, $needle, $offset=0){
			if(!is_array($needle)){
				$needle = [$needle];
			}
			$ll = null;
			foreach($needle as $item){
				$l = strlen($item);
				if(!isset($s) || $ll != $l){
					$s = read_after($string,$position,$l,$offset);
					$ll = $l;
				}
				if($s === $item) return true;
			}
			return false;
		}

	}

	class Capture{

		public $position;

		public $type;

	}

	class Tokenizer{

		public $captures = [];


		/**
		 * @param $type
		 * @return bool
		 */
		public function hasCapture($type){
			foreach($this->captures as $capture){
				if($capture->type === $type){
					return true;
				}
			}
			return false;
		}

		/**
		 * @return mixed|null
		 */
		public function getCurrentCapture(){
			return $this->captures?current(array_slice($this->captures,-1,1)):null;
		}

	}

	/**
	 * Class Block
	 * @package Jungle\Util\Path
	 */
	class Block{

		public $type;

		public $start_position;

		public $cache;


		/**
		 * Block constructor.
		 * @param $start_position
		 */
		public function __construct($start_position){

		}

		public function char($char, $pos){

		}

	}

}


