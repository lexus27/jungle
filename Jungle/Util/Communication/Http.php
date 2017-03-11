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


		/**
		 * parse queue list ... ; ... ; ...  ,  ... ; ... ; ...  ,  ...
		 * delimiter [,]
		 * element delimiter [;]
		 *
		 * delimiter parse  '@( [^,"'\\]+ | \\\\ | \\, | (?:(')|(")) (?(-1) (\\\\|\\"|[^"]+)* | (\\\\|\\\\\\'|[^']+)*) (?(-1)"|') )*@'
		 * element parse    '@( [^;"'\\]+ | \\\\ | \\; | (?:(')|(")) (?(-1) (\\\\|\\"|[^"]+)* | (\\\\|\\\\\\'|[^']+)*) (?(-1)"|') )*@'
		 *
		 * @param $accept
		 * @return array
		 */
		public static function parseAccept($accept){
			$accept = explode(',', $accept);
			$q = [];
			foreach($accept as $i => $item){
				if(!isset($q[$i])){
					$q[$i] = [];
				}
				$item = explode(';',trim($item));
				foreach($item as $value){
					if(stripos($value,'=')!==false){
						list($key,$value) = array_replace([null,null],explode('=',trim($value),2));
						$q[$i][trim($key)] = trim($value);
					}else{
						$q[$i][] = trim($value);
					}
				}
			}
			unset($accept,$i,$item,$key,$value);

			$a = [];
			foreach($q as $value){
				if(isset($value['q'])){
					$priority = $value['q'];
					unset($value['q']);
					$a[$priority] = isset($a[$priority])?array_merge($a[$priority], $value):$value;
				}else{
					$a['1'] = isset($a['1'])?array_merge($a['1'], $value):$value;
				}
			}
			unset($q,$priority, $value);
			krsort($a);
			$accept = [];
			foreach($a as $q){
				foreach($q as $item){
					$accept[] = $item;
				}
			}
			return $accept;
		}

		/**
		 * @param array $accept
		 * @return string
		 */
		public static function renderAccept(array $accept){
			$accept = array_reverse($accept, false);
			$count = count($accept);
			$q = [];
			foreach($accept as $i => $item){
				$p = round((((100 / $count) * ($i+1)) / 100),1);
				$q[] =  $item . ($p<1?(';q='.$p):'');
			}
			return implode(',', array_reverse($q));
		}



	}
}

