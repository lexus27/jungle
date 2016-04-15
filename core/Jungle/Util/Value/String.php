<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 15:25
 */
namespace Jungle\Util\Value {

	use Jungle\RegExp;

	/**
	 * Class String
	 * @package Jungle\Util\Value
	 */
	class String{

		/** @var string */
		protected static $default_camelize_delimiter = '_';

		/** @var array */
		protected static $camelize_word_delimiters = ['-','_'];


		protected function __construct(){}

		/**
		 * @param $value
		 * @param array $parameters
		 * @return string
		 */
		public static function representFrom($value,array $parameters = []){

			Massive::applyIf($parameters,[
				'bool'              => [FALSE,TRUE],
				'null'              => 'NULL',
				'empty'             => 'EMPTY',
				'empty_type_render' => true
			]);

			if(is_bool($value)){
				if(!isset($parameters['bool'])){
					$parameters['bool'] = [];
				}
				$parameters['bool'][0] = isset($parameters['bool'][0])?$parameters['bool'][0]:'FALSE';
				$parameters['bool'][1] = isset($parameters['bool'][1])?$parameters['bool'][1]:'TRUE';
				$string = $parameters['bool'][intval($value)];
			}elseif(is_null($value)){
				if(!isset($parameters['null'])){
					$parameters['null'] = 'NULL';
				}
				$string =  $parameters['null'];
			}elseif(empty($value) && isset($parameters['empty'])){
				if(!is_string($parameters['empty']) || !$parameters['empty']){
					$parameters['empty'] = 'EMPTY';
				}
				$typePrint = isset($parameters['empty_type_render'])?boolval($parameters['empty_type_render']):true;
				$string =  $parameters['empty'].($typePrint?'('.gettype($value).')':'');
			}else{
				return (string)$value;
			}
			$lover = isset($parameters['lover'])?$parameters['lover']:null;
			return $lover===null?$string:($lover?mb_strtolower($string):mb_strtoupper($string));
		}

		/**
		 * @param $value
		 * @param array $parameters
		 * @return string
		 */
		public static function convertToActualType($value,array $parameters = []){
			$parameters['bool'] = isset($parameters['bool']) && is_array($parameters['bool'])?$parameters['bool']:[];
			$parameters['bool'][0] = isset($parameters['bool'][0])?$parameters['bool'][0]:'FALSE';
			$parameters['bool'][1] = isset($parameters['bool'][1])?$parameters['bool'][1]:'TRUE';

			$parameters['null'] = isset($parameters['null'])?$parameters['null']:'NULL';

			$parameters['empty'] = (
				isset($parameters['empty']) ||
				is_string($parameters['empty']) &&
				$parameters['empty']
			)? $parameters['empty']:'EMPTY';

			$parameters['empty_type_render'] = isset($parameters['empty_type_render'])?
				boolval($parameters['empty_type_render']):true;

			$v = trim($value);
			if(self::match($v,$parameters['bool'][0],true)){
				return false;
			}else if(self::match($v,$parameters['bool'][1],true)){
				return true;
			}else if(self::match($v,$parameters['null'],true)){
				return null;
			}elseif(preg_match('@(?:'.implode('|',$parameters['empty']).')'.'(\(\w+\))?@',$value,$matches)){
				$v = null;
				if($matches[1]) settype($v,$matches[1]);
				return $v;
			}else{
				return $value;
			}
		}

		/**
		 * @param string $string
		 * @param string $prefix
		 * @param string $suffix
		 * @return string
		 */
		public static function cover($string, $prefix = '', $suffix = ''){
			return $prefix . $string . $suffix;
		}

		/**
		 * @param $string
		 * @param string $prefix
		 * @param string $suffix
		 * @param bool|false $caseLess
		 * @return bool
		 */
		public static function isCovered($string, $prefix = null,$suffix = null,$caseLess = false){
			if(!$prefix && !$suffix){
				return true;
			}
			$r = true;
			if($suffix!==null){
				$r = self::endWith($suffix,$string,$caseLess);
			}
			if($prefix!==null && $r){
				$r = self::startWith($prefix,$string,$caseLess);
			}
			return $r;
		}

		/**
		 * @param string    $string
		 * @param null      $charListLeft
		 * @param null      $charListRight
		 * @return string
		 */
		public static function trimSides($string,$charListLeft=null,$charListRight=null){
			if(!$charListLeft && !$charListRight) return $string;
			$string = $charListLeft?ltrim($string,$charListLeft):$string;
			return $charListRight?rtrim($string,$charListRight):$string;
		}

		/**
		 * @param string            $string
		 * @param string[]|string   $words_list
		 * @param bool              $caseLess
		 * @return string
		 */
		public static function trimWords($string,$words_list,$caseLess = false){
			if(!$words_list){
				return $string;
			}
			if(!is_array($words_list)){
				$words_list = [$words_list];
			}
			$words_list = implode('|',array_map(function($word){
				return preg_quote($word,'@');
			},$words_list));
			return preg_replace('@^('.$words_list.')(.*?)('.$words_list.')$@sm'.($caseLess?'i':''),'\\2',$string);
		}

		/**
		 * @param string                $string
		 * @param string[]|string|null  $wordsListLeft
		 * @param string[]|string|null  $wordsListRight
		 * @param bool $caseLess
		 * @return string
		 */
		public static function trimWordsSides($string,$wordsListLeft = null, $wordsListRight = null,$caseLess = false){
			if(!$wordsListLeft && !$wordsListRight) return $string;
			if($wordsListLeft){
				if(!is_array($wordsListLeft)) $wordsListLeft = [$wordsListLeft];
				$wordsListLeft = implode('|',array_map(function($word){
					return preg_quote($word,'@');
				},$wordsListLeft));
			}else{
				$wordsListLeft = '';
			}
			if($wordsListRight){
				if(!is_array($wordsListRight)) $wordsListRight = [$wordsListRight];
				$wordsListRight = implode('|',array_map(function($word){
					return preg_quote($word,'@');
				},$wordsListRight));
			}else{
				$wordsListRight = '';
			}

			return preg_replace('@^('.$wordsListLeft.')(.*?)('.$wordsListRight.')$@sm'.($caseLess?'i':''),'\\2',$string);
		}

		/**
		 * @param string            $string
		 * @param string[]|string   $words_list
		 * @param bool              $caseLess
		 * @return string
		 */
		public static function trimWordsLeft($string,$words_list,$caseLess = false){
			return self::trimWordsSides($string,$words_list,null,$caseLess);
		}

		/**
		 * @param string            $string
		 * @param string[]|string   $words_list
		 * @param bool              $caseLess
		 * @return string
		 */
		public static function trimWordsRight($string,$words_list,$caseLess = false){
			return self::trimWordsSides($string,null,$words_list,$caseLess);
		}

		/**
		 * @param string            $string
		 * @param string[]|string   $comparable
		 * @param callable          $comparableFunction
		 * @return bool
		 */
		public static function universalMatch($string,$comparable,callable $comparableFunction = null){
			if($comparableFunction===null)$comparableFunction = 'strcmp';
			if(is_array($comparable)){
				foreach($comparable as $s){
					if(call_user_func($comparableFunction,$string,$s)===0){
						return true;
					}
				}
				return false;
			}else{
				return call_user_func($comparableFunction,$string,$comparable)===0;
			}
		}

		/**
		 * @param $ch
		 * @return bool
		 */
		public static function isSpecialChar($ch){
			return in_array($ch,['\\','/','!','@','#','$','%','^','&','*','(',')','-','+','|',':',';','"','\'','{','}','[',']','?','.',',','<','>','~','`','№'],true);
		}

		/**
		 * @param $char
		 * @return bool
		 */
		public static function isWordChar($char){
			return preg_match('@^[\w]$@',$char)>0;
		}

		/**
		 * @param $char
		 * @return bool
		 */
		public static function isDigitChar($char){
			return preg_match('@^[\d]$@',$char)>0;
		}


		/**
		 * @param string            $string
		 * @param string[]|string   $comparable
		 * @param bool              $caseLess
		 * @return bool
		 */
		public static function match($string,$comparable,$caseLess = false){
			return self::universalMatch($string,$comparable,$caseLess?'strcasecmp':'strcmp');
		}

		/**
		 * @param string[]|string $startWith
		 * Если $startWith является массивом, то они определяются как "ИЛИ",
		 *
		 * @param string[]|string $comparable
		 * @param bool $caseLess
		 * @param bool $regExp
		 * @return bool
		 */
		public static function startWith($startWith,$comparable,$caseLess = false, $regExp = false){
			if(is_array($startWith)){
				foreach($startWith as $s){
					if(self::startWith($s,$comparable,$caseLess)){
						return true;
					}
				}
				return false;
			}else{
				$length = mb_strlen($startWith);
				if(is_array($comparable)){
					foreach($comparable as $s){
						$starting = mb_substr($s, 0, $length);
						if($caseLess){
							if(strcasecmp($starting,$startWith)===0){
								return true;
							}
						}else{
							if(strcmp($starting,$startWith)===0){
								return true;
							}
						}
					}
					return false;
				}else{
					$starting = mb_substr($comparable, 0, $length);
					if($caseLess){
						return strcasecmp($starting,$startWith)===0;
					}else{
						return strcmp($starting,$startWith)===0;
					}
				}
			}
		}

		/**
		 * @param string $endWith
		 * @param string[]|string $comparable
		 * @param bool $caseLess
		 * @param bool $regExp
		 * @return bool
		 */
		public static function endWith($endWith,$comparable,$caseLess = false, $regExp = false){
			if(is_array($endWith)){
				foreach($endWith as $s){
					if(self::endWith($s,$comparable,$caseLess)){
						return true;
					}
				}
				return false;
			}else{
				$length = mb_strlen($endWith);
				if(is_array($comparable)){
					foreach($comparable as $s){
						$ending = mb_substr($s, -$length);
						if($caseLess){
							if(strcasecmp($ending,$endWith)===0){
								return true;
							}
						}else{
							if(strcmp($ending,$endWith)===0){
								return true;
							}
						}
					}
					return false;
				}else{
					$ending = mb_substr($comparable, -$length);
					if($caseLess){
						return strcasecmp($ending,$endWith)===0;
					}else{
						return strcmp($ending,$endWith)===0;
					}
				}
			}
		}






		/**
		 * @param $string
		 * @return string
		 */
		public static function lcFirst($string){
			return mb_strtolower(mb_substr($string,0,1)).mb_substr($string,1);
		}

		/**
		 * @param $string
		 * @return string
		 */
		public static function ucFirst($string){
			return mb_strtoupper(mb_substr($string,0,1)).mb_substr($string,1);
		}

		/**
		 * @param $string
		 * @return mixed
		 */
		public static function ucWords($string){
			static $fn;if(!$fn) $fn = function ($matches){
				return $matches[1] . mb_strtoupper($matches[2]);
			};

			return preg_replace('@(^|\s)([a-zа-я])@e', $fn, $string);
		}

		/**
		 * @param $string
		 * @return mixed
		 */
		public static function lcWords($string){
			static $fn;if(!$fn) $fn = function($matches){
				return $matches[1].mb_strtolower($matches[2]);
			};
			return preg_replace('@(^|\s)([a-zа-я])@e',$fn,$string);
		}

		/**
		 * Decamelize text
		 *
		 * @param string $text camelize text eg. TestCamelize
		 * @param null $delimiter
		 * @return string decamelize text eg. test_camelize
		 *
		 * @example Camelize::decamelize('TestCamelize');
		 */
		public static function uncamelize($text,$delimiter = null) {
			if(!$delimiter)$delimiter = self::$default_camelize_delimiter;
			return mb_strtolower(preg_replace('/(?:(^|[a-zа-я])|(\s+)|(\W))([A-ZА-Я])/e', '"\\3"? "\\0" :("\\2"? "'.$delimiter.'\\4" :(mb_strlen("\\1")? "\\1'.$delimiter.'\\4" : "\\4"))', $text));
		}



		/**
		 * Camelize text
		 *
		 * @param string $text decamelize text eg. test_camelize
		 * @param $wordDelimiters
		 * @param bool $firstLover
		 * @return string camelize text eg. TestCamelize
		 *
		 * @example Camelize::camelize('test_camelize');
		 */
		public static function camelize($text,$wordDelimiters = null,$firstLover = false) {
			if(!$wordDelimiters)$wordDelimiters = self::$camelize_word_delimiters;
			$s = self::uncamelize($text);
			$s = preg_replace('/(^|\s+|'.implode('|',RegExp::pregQuoteArray($wordDelimiters,'/')).')([a-zа-я])/ei', 'mb_strtoupper("\\2")', $s);
			return $firstLover?self::lcFirst($s):$s;
		}

		/**
		 * @param $text
		 * @param bool|true $camel
		 * @param null $delimiter
		 * @return string
		 */
		public static function camelCase($text, $camel = true, $delimiter = null){
			return $camel?
				self::camelize(self::uncamelize($text,$delimiter)):
				self::uncamelize(self::camelize($text),$delimiter);
		}


		/**
		 * @param $string
		 * @return bool
		 */
		public static function isUpper($string){
			return mb_strtoupper($string) === $string;
		}

		/**
		 * @param $string
		 * @return bool
		 */
		public static function isLower($string){
			return mb_strtolower($string) === $string;
		}



		/**
		 * TODO
		 * @param $string
		 * @param int $value
		 * @param null $delimiter
		 * @param null $afterNumerable
		 * @return mixed
		 * @internal param null $pattern
		 */
		public static function increment($string, $value = 1, $delimiter = null, $afterNumerable = null){

		}

		/**
		 * TODO
		 * @param $string
		 * @param int $value
		 * @param null $pattern
		 */
		public static function decrement($string, $value = 1, $delimiter = null, $pattern = null){

		}


	}
}

