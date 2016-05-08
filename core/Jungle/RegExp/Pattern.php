<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.04.2016
 * Time: 22:20
 */
namespace Jungle\RegExp {

	/**
	 * @Static
	 * Class Pattern
	 * @package Jungle\RegExp
	 */
	class Pattern{

		/**
		 * @var array
		 */
		protected static $not_capturing_groups = [ '?#', '?:', '?>', '?=', '?!', '?<=', '?<!' ];


		/**
		 * @param $pattern
		 * @param string $modifiers
		 * @param string $delimiter
		 * @return string
		 */
		public static function escape($pattern, $modifiers = '', $delimiter = '@'){
			return $delimiter . addcslashes($pattern, $delimiter) . $delimiter . $modifiers;
		}

		/**
		 * @param $pattern
		 * @param $value
		 * @param string $modifiers
		 * @return bool
		 */
		public static function validateValue($pattern, $value, $modifiers = 'S'){
			return preg_match('@^' . addcslashes($pattern, '@') . '$@' . $modifiers, $value) > 0;
		}

		/**
		 * @param $pattern
		 * @return bool
		 */
		public static function hasCaptures($pattern){
			$len = strlen($pattern);
			for($i = 0; $i < $len; $i++){
				$token = $pattern{$i};
				if($token === '('){
					for($backslashes = 0; read_before($pattern, $i, 1, $backslashes) === '\\'; $backslashes++){
					}
					if($backslashes % 2 == 0){
						if(!self::byte_has_after($pattern, $i, self::$not_capturing_groups)){
							return true;
						}
					}
				}
			}
			return false;

		}

		/**
		 * @param $pattern
		 * @return array
		 */
		public static function analyzeGroups($pattern){
			$len = strlen($pattern);
			$total_opened = 0;
			$opened = [ ];
			$captured_groups = [ ];
			$transparent_groups = [ ];
			for($i = 0; $i < $len; $i++){
				$token = $pattern{$i};
				if($token === '('){
					for($backslashes = 0; self::byte_read_before($pattern, $i, 1, $backslashes) === '\\'; $backslashes++){
					}
					if($backslashes % 2 == 0){
						$capture = !self::byte_has_after($pattern, $i, self::$not_capturing_groups);
						$opened[] = [ $i, $capture, $total_opened ];
						$total_opened++;
					}
				}elseif($token === ')'){
					for($backslashes = 0; self::byte_read_before($pattern, $i, 1, $backslashes) === '\\'; $backslashes++){
					}
					if($backslashes % 2 == 0){
						if($opened){
							list($pos, $capture, $index) = array_pop($opened);
							if($capture){
								$captured_groups[] = [ $pos, $i, $index ];
							}else{
								$transparent_groups[] = [ $pos, $i, $index ];
							}
						}else{
							throw new \LogicException('Error have not expected closed groups!');
						}
					}
				}
			}
			if($opened){
				throw new \LogicException(
					'Error have not closed opened groups by offset at \'' .
					implode('\' and \'', array_column($opened, 0)) . '\''
				);
			}
			$u = function ($d1, $d2){
				$a = $d1[0];
				$b = $d2[0];
				return ($a === $b ? 0 : (($a < $b) ? -1 : 1));
			};
			usort($captured_groups, $u);
			usort($transparent_groups, $u);
			return [
				'total'       => $total_opened,
				'captured'    => $captured_groups,
				'transparent' => $transparent_groups
			];
		}

		/**
		 * @param $string
		 * @param $position
		 * @param int $len
		 * @param int $offset
		 * @return string
		 */
		static function byte_read_after($string, $position, $len = 1, $offset = 0){
			return substr($string, $position + 1 + $offset, $len);
		}

		/**
		 * @param $string
		 * @param $position
		 * @param int $len
		 * @param int $offset
		 * @return string
		 */
		static function byte_read_before($string, $position, $len = 1, $offset = 0){
			$pos = $position - $offset;
			$start = $pos - $len;
			if($start < 0){
				$len += $start;
				if(!$len) return '';
				$start = 0;
			}
			return substr($string, $start, $len);
		}

		/**
		 * @param $string
		 * @param $position
		 * @param $needle
		 * @param int $offset
		 * @return bool
		 */
		static function byte_has_before($string, $position, $needle, $offset = 0){
			if(!is_array($needle)){
				$needle = [ $needle ];
			}
			$ll = null;
			foreach($needle as $item){
				$l = strlen($item);
				if(!isset($s) || $ll != $l){
					$s = read_before($string, $position, $l, $offset);
					$ll = $l;
				}
				if($s === $item) return true;
			}
			return false;
		}

		/**
		 * @param $string
		 * @param $position
		 * @param $needle
		 * @param int $offset
		 * @return bool
		 */
		static function byte_has_after($string, $position, $needle, $offset = 0){
			if(!is_array($needle)){
				$needle = [ $needle ];
			}
			$ll = null;
			foreach($needle as $item){
				$l = strlen($item);
				if(!isset($s) || $ll != $l){
					$s = read_after($string, $position, $l, $offset);
					$ll = $l;
				}
				if($s === $item) return true;
			}
			return false;
		}


	}
}

