<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.07.2016
 * Time: 19:04
 */
namespace Jungle {
	
	/**
	 * Class AssetsManager
	 * @package Jungle
	 */
	class AssetsManager{
		
		protected $styles = [];
		
		protected $top_scripts = [];
		
		protected $bottom_scripts = [];


		/**
		 * @param $asset
		 */
		public function get($asset){

		}




		/**
		 * @return string
		 */
		public function getPublicDirectory(){
			return $_SERVER['DOCUMENT_ROOT'];
		}
		
		/**
		 * @param $assets_path
		 */
		public function isPhantomPath($assets_path){
			$pubDir = $this->getPublicDirectory();
			
			if(strpos($assets_path, $pubDir)===0){
				
			}
			
		}

		/**
		 * @param $assets_path
		 * @return string
		 */
		public function getPublicPathname($assets_path){
			$pub_dir = $this->getPublicDirectory();
			if(strpos($assets_path, $pub_dir)===0){
				$pub_pathname = substr($assets_path,strlen($pub_dir));
				return $pub_pathname;
			}else{



			}
		}


		public function transformCoreToPublic($pathname){
			$info = pathinfo($pathname);
			$basename   = $info['basename'];
			$filename   = $info['filename'];
			$dirname    = $info['dirname'];
			$dir_name = basename($dirname);
			$pub_dir = $this->getPublicDirectory();
			$transformed = 'transformed';
			$new_pathname = $pub_dir . DIRECTORY_SEPARATOR . $transformed . DIRECTORY_SEPARATOR . $dir_name . DIRECTORY_SEPARATOR . $basename;

			if(file_exists($new_pathname)){

			}

			if(copy($pathname, $new_pathname)){



			}


			return $new_pathname;
		}
		
		public function drawStyles(){
			
		}
		
		public function drawTopScripts(){
			
		}
		
		public function drawBottomScripts(){
			
		}


		protected $base_dirname;

		protected $base_relative;

		/**
		 * @param $absolute_path
		 * @return string|bool
		 */
		public function relative($absolute_path){
			if(strpos($absolute_path,$this->base_dirname) === 0){
				return substr($absolute_path, strlen($this->base_dirname));
			}else{
				return false;
			}
		}

		/**
		 * Захват означает, перемещение или создание ярлыка для структуры
		 * находящейся по абсолютному пути и не находящимся в базовой публичной директории
		 * @param $absolute_path
		 * @return bool|string
		 */
		public function capture($absolute_path){
			$relative = $this->relative($absolute_path);
			if($relative === false){
				/**
				 * Как мы из абсолютного пути , получим относительный:
				 * С какой точки отрезать абсолютный путь чтобы получить относительный?
				 * Какое имя файла будет в таком случае?
				 * В какую относительную директорию положить полученый файл или папку?
				 */

				/**
				 * Захешировать абсолютный путь по каким либо правилам
				 *      Захешировать dirname, оставив базовое имя "ноды"
				 * Знать где отрезать абсолютный путь для получения относительного пути
				 *
				 */
			}
			return $relative;
		}

		/**
		 * @param $relative_path
		 * @return string
		 */
		public function absolute($relative_path){
			return $this->base_dirname . DIRECTORY_SEPARATOR . ltrim($relative_path,'\/');
		}

		
	}
}

