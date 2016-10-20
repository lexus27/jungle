<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.10.2016
 * Time: 13:11
 */
namespace Jungle\Util\Communication\Hypertext {
	
	class Media{

		/**
		 * @param string|array $mediaType
		 * @return array|false
		 * @throws \Exception
		 */
		public static function parseMediaType($mediaType){
			if(!is_array($mediaType)){
				if(!is_string($mediaType)){
					throw new \Exception('Media type must be string for parsing');
				}
				$mediaType = explode('/', trim($mediaType), 2);
			}else{
				$mediaType = array_slice($mediaType,0,2);
			}
			return !$mediaType? false : array_replace([null,null],$mediaType);
		}

		/**
		 * @param $type_token
		 * @return bool
		 */
		public static function isMixedToken($type_token){
			return $type_token === '*' || $type_token === null;
		}

		/**
		 *
		 * @param $b
		 * @param $a
		 * @return bool
		 */
		public static function checkMediaType($a, $b){
			if(!is_array($a)) $a = self::parseMediaType($a);
			if(!is_array($b)) $b = self::parseMediaType($b);
			return self::_pattern_a($a,$b);
		}

		/**
		 * @param $desired
		 * @param $general
		 * @return bool
		 */
		protected static function _check($desired, $general){
			return strcasecmp($desired,$general)===0 ||
			       self::isMixedToken($desired) ||
			       self::isMixedToken($general);
		}



		/**
		 * @Experimental
		 * @TODO Приспособить какой-то ОО Паттерн, для вычесление вложенных правил по такому типу, где вложений может быть более 2х в отличие от данной функции
		 * @param array $a
		 * @param array $b
		 * @return bool
		 */
		protected static function _pattern_a(array $a,array $b){
			$a_item = array_shift($a);
			$b_item = array_shift($b);

			//checking
			if(strcasecmp($a_item,$b_item)===0){
				return self::_pattern_a($a,$b);
			}elseif(self::isMixedToken($a_item)){
				return self::_pattern_a($a,$b);
			}elseif(self::isMixedToken($b_item)){
				return self::_pattern_a($a,$b);
			}

			if(!$a && !$b){
				return false;
			}
			return false;
		}



	}
}

