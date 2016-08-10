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
		
	}
}

