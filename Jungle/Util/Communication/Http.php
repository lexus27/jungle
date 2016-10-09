<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 04.10.2016
 * Time: 20:51
 */
namespace Jungle\Util\Communication {
	
	/**
	 * Class Http
	 * @package Jungle\Util\Communication
	 */
	class Http{

		/**
		 * @param $string
		 * @param bool $decode
		 * @param string $separator
		 * @param string $define
		 * @return array
		 */
		public static function parseCookieString($string, $decode = true, $separator = ';', $define = '='){
			$a = explode($separator,$string);
			list($key,$value) = explode($define,trim(array_shift($a)),2);
			$o = [
				'key'       => $key,
				'value'     => $value,
				'path'      => null,
				'domain'    => null,
				'expires'   => null,
				'secure'    => null,
				'httpOnly'  => null,
			];
			if($decode){
				$o['key']       = urldecode($key);
				$o['value']     = urldecode($value);
			}

			foreach($a as $i => $pair){
				$pair = trim($pair);
				if($i <= 2){
					list($key,$value) = explode('=',trim($pair),2);
					if($decode){
						$o[urldecode($key)] = urldecode($value);
					}
				}elseif(strcasecmp($pair,'httpOnly')===0){
					$o['httpOnly']  = true;
				}elseif(strcasecmp($pair,'secure')===0){
					$o['secure']    = true;
				}
			}

			$o['expires']   = $o['expires']?(strtotime($o['expires'])):null;

			return $o;
		}



	}
}

