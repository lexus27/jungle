<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.03.2016
 * Time: 19:54
 */
namespace Jungle\Util\Value {

	/**
	 * Class RegExp
	 * @package Jungle\Util\Value
	 */
	class RegExp{

		/**
		 * @param $regExp
		 */
		public static function countMask($regExp){

		}

		public static function exportPattern($regExp){

		}

		/**
		 * @param $regExp
		 * @param string $delimiter
		 * @return string
		 */
		public static function quote($regExp,$delimiter = '/'){

		}

		/**
		 * @param $regExp
		 * @param $subject
		 * @param $matches
		 * @param int $flags
		 * @param int $offset
		 * @return int
		 */
		public static function match($regExp,$subject,array & $matches = null, $flags=0, $offset=0){
			return preg_match($regExp,$subject,$matches,$flags,$offset);
		}

		/**
		 * @param $regExp
		 * @param $subject
		 * @param $matches
		 * @param int $flags
		 * @param int $offset
		 * @return int
		 */
		public static function matchAll($regExp,$subject,array & $matches = null, $flags=0, $offset=0){
			return preg_match_all($regExp,$subject,$matches,$flags,$offset);
		}

		/**
		 * @param $regExp
		 * @param $replacement
		 * @param $subject
		 * @param int $limit
		 * @param null $count
		 * @return mixed
		 */
		public static function replace($regExp, $replacement, $subject, $limit=-1, & $count = null){
			if(is_callable($replacement)){
				return preg_replace_callback($regExp,$replacement,$subject,$limit,$count);
			}else{
				return preg_replace($regExp,$replacement,$subject,$limit,$count);
			}
		}

		/**
		 * @param $regExp
		 * @param $subject
		 * @param int $limit
		 * @param int $flags
		 * @return array
		 */
		public static function split($regExp,$subject, $limit=-1, $flags=0){
			return preg_split($regExp,$subject,$limit,$flags);
		}

	}
}

