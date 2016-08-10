<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 21.03.2016
 * Time: 20:10
 */
namespace Jungle\Util\Value {

	/**
	 * Class Cmp
	 * @package Jungle\Util\Value
	 */
	class Cmp{

		/**
		 * @param int $result
		 * @return int
		 */
		public static function invert($result){
			if($result>0){
				return -1;
			}elseif($result<0){
				return 1;
			}else{
				return 0;
			}
		}

		/**
		 * @param array $results
		 * @return int
		 */
		public static function product(array $results){
			foreach($results as $result){
				if($result !== 0){
					return $result;
				}
			}
			return 0;
		}

	}
}

