<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.01.2016
 * Time: 2:20
 */
namespace Jungle {

	/**
	 * Class FileSystem
	 * @package Jungle
	 */
	class FileSystem{

		const DS = DIRECTORY_SEPARATOR;

		/**
		 * @param $path
		 * @return null|string
		 */
		public static function getDiskName($path){
			if(strlen($path)===1 && !is_numeric($path)){
				return strtoupper($path);
			}
			if(preg_match('@^(\w):@',$path,$m)){
				return strtoupper($m[1]);
			}
			return null;
		}

		/**
		 * @param $dirname
		 * @param string $directorySeparator
		 * @return string
		 * pass path/to/directory
		 * return path/to/directory/
		 */
		public static function dirnameSlash($dirname, $directorySeparator = null){
			if(null === $directorySeparator){
				$directorySeparator = self::DS;
			}
			return rtrim($dirname,'\\/') . $directorySeparator;
		}

		/**
		 * Нормализация путей для $directorySeparator (@see DIRECTORY_SEPARATOR)
		 * @param $path
		 * @param bool $includeFolding if true all double DIRECTORY_SEPARATOR and non current separator conversion to one DIRECTORY_SEPARATOR
		 * @param null $directorySeparator defaults is DIRECTORY_SEPARATOR
		 * @return string
		 */
		public static function normalizePath($path, $includeFolding = false, $directorySeparator = null){
			if(null===$directorySeparator) $directorySeparator = self::DS;
			if($includeFolding){
				$doubleSep = str_repeat(preg_quote(self::revertPathSeparator($directorySeparator),'@'),2);
				return preg_replace_callback('@(?::('.$doubleSep.'))|([/\\\\]+)@S',function($m)use($directorySeparator){
					return ($m[1]?$directorySeparator.$directorySeparator:$directorySeparator);
				}, $path);
			}
			return strtr($path,self::revertPathSeparator($directorySeparator), self::DS);
		}

		/**
		 * @param $separator
		 * @return string
		 */
		public static function revertPathSeparator($separator = null){
			if(null===$separator) $separator = self::DS;
			return $separator === '\\'?'/':'\\';
		}

		/**
		 * @param $path
		 */
		public static function remove($path){
			if(is_file($path)){
				chmod($path,0777);
				unlink($path);
				return;
			}
			if(is_dir($path)){
				$path = rtrim($path,'\/') . DIRECTORY_SEPARATOR;
				foreach(glob($path . '*') as $child){
					self::remove($child);
				}
				chmod($path,0777);
				rmdir($path);
				return;
			}
		}

		/**
		 * @param $dirname
		 */
		public static function removeContain($dirname){
			if(is_dir($dirname)){
				$path = rtrim($dirname,'\/') . DIRECTORY_SEPARATOR;
				foreach(glob($dirname . '*') as $child){
					self::remove($child);
				}
			}
		}

	}
}

