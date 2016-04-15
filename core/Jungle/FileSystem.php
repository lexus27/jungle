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

		public static function move(){

		}

		/**
		 * @param $path
		 * @param $newBase
		 * @param bool $overwrite
		 * @param null $destinationDirectory
		 */
		public static function copy($path, $newBase, $overwrite = true,  $destinationDirectory = null){

			if(file_exists($path)){

				$dir = pathinfo($path,PATHINFO_DIRNAME);
				$base = pathinfo($path,PATHINFO_BASENAME);

				$newDir = $destinationDirectory?rtrim($destinationDirectory,'/\\'):$dir;
				if(!$newBase){
					$newBase = $base;
				}
				$new = rtrim($newDir . DIRECTORY_SEPARATOR . $newBase,'/\\');

				if(is_dir($path)){

					if(is_dir($new)){



					}else{

						mkdir($new,'0777',true);

					}

					foreach(glob(rtrim($path,'/\\').DIRECTORY_SEPARATOR .'*') as $p){
						self::copy($p,null,$overwrite,$new . DIRECTORY_SEPARATOR);
					}
				}else{

					if(file_exists($new)){

						if($overwrite){

							unlink($new);

						}else{

							throw new \LogicException('File already exists in destination');

						}

					}

					copy($path,$new);
				}

			}


		}

		/**
		 * @param $path
		 * @param callable $callback
		 * @return bool|mixed
		 */
		public static function check($path,callable $callback){
			if(file_exists($path)){
				if(is_dir($path)){
					foreach(glob(rtrim($path,'\\/') .DIRECTORY_SEPARATOR. '*') as $p){
						if(self::check($p,$callback)===false){
							return false;
						}
					}
					return call_user_func($callback,$path)!==false;
				}else{
					return call_user_func($callback,$path)!==false;
				}
			}
			return true;
		}

		/**
		 * @param $path
		 * @param callable $callback
		 * @return bool
		 */
		public static function each($path,callable $callback){
			if(file_exists($path)){
				if(is_dir($path)){
					foreach(glob(rtrim($path,'\\/') .DIRECTORY_SEPARATOR. '*') as $p){
						self::check($p,$callback);
					}
					call_user_func($callback,$path);
				}else{
					call_user_func($callback,$path);
				}
			}
		}

		/**
		 * @param $dirPath
		 * @return bool|mixed
		 */
		public static function isWritable($dirPath){
			static $fn;
			if(!$fn)$fn = function($p){return is_writable($p);};
			return self::check($dirPath,$fn);
		}


		/**
		 * @param $path
		 * @param bool $mutable
		 * @return bool
		 */
		public static function delete($path, $mutable = true){
			if(file_exists($path)){
				@chmod($path,0777);
				if(!is_writable($path)){
					throw new \LogicException('DELETE ERROR: access denied permission to absolute  "'.$path.'"');
				}
				if(is_dir($path)){
					foreach(glob(rtrim($path,'\\/') .DIRECTORY_SEPARATOR. '*') as $p){
						self::delete($p);
					}
					if(!@rmdir($path)){
						$error = error_get_last();
						throw new \LogicException('DELETE ERROR: message ['.$error['message'].'] "'.$path.'"');
					}
				}else{
					if(!@unlink($path)){
						$error = error_get_last();
						throw new \LogicException('DELETE ERROR: message ['.$error['message'].'] "'.$path.'"');
					}
				}
			}elseif(!$mutable){
				throw new \LogicException('DELETE ERROR: access to not exists absolute "'.$path.'"');
			}
			return true;
		}

		/**
		 * @param $path
		 * @param $newName
		 * @param bool|true $saveFileExtension
		 * @return bool
		 */
		public static function rename($path, $newName, $saveFileExtension = true){
			if(file_exists($path)){

				if(!is_writable($path)){
					throw new \LogicException('RENAME ERROR: access denied permission to absolute  "'.$path.'"');
				}

				$ending = '';
				if(!is_dir($path)){
					$extension  = pathinfo($path,PATHINFO_EXTENSION);
					$ending = ($saveFileExtension?$extension:'');
				}
				$dir        = pathinfo($path,PATHINFO_DIRNAME);
				$basename   = pathinfo($path,PATHINFO_BASENAME);
				$newBaseName    = $newName . $ending;
				$newDir         = $dir;
				$newPath        = $newDir . DIRECTORY_SEPARATOR . $newBaseName;
				if($basename !== $newBaseName){
					if(file_exists($newPath)){
						throw new \LogicException('RENAME ERROR: new name "'.$newPath.'" already unavailable');
					}
					if(!rename($path,$newPath)){
						throw new \LogicException('RENAME ERROR: "'.$path.'" to "'.$newPath.'" not renamed!');
					}
				}
			}else{
				throw new \LogicException('RENAME ERROR: is not exists real element by absolute "'.$path.'"');
			}
			return true;
		}

		/**
		 * @param $path1
		 * @param $path2
		 * @return int
		 */
		public static function comparePathNames($path1, $path2){
			return strcasecmp(self::normalizePathName($path1),self::normalizePathName($path2));
		}

		/**
		 * @param $path
		 * @return string
		 */
		public static function normalizePathName($path){
			return rtrim(preg_replace('@[\\\\/]+@',DIRECTORY_SEPARATOR,$path),'\/');
		}



	}
}

