<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 29.01.2016
 * Time: 18:32
 */
namespace Jungle\FileSystem {

	/**
	 * Class Object
	 * @package Jungle\FileSystem
	 */
	abstract class AObject{

		/** @var string */
		protected $path;

		/**
		 * @param $path
		 * @return $this
		 */
		public function setPath($path){
			$this->path = $path;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getPath(){
			return $this->path;
		}



		/** @return bool */
		public function isExists(){
			return file_exists($this->path);
		}

		/** @return bool */
		public function isDir(){
			return is_dir($this->path);
		}

		/** @return bool */
		public function isFile(){
			return is_file($this->path);
		}

		/** @return bool */
		public function isReadable(){
			return is_readable($this->path);
		}

		/** @return bool */
		public function isWritable(){
			return is_writable($this->path);
		}

		/** @return bool */
		public function isLink(){
			return is_link($this->path);
		}

		/** @return bool */
		public function isExecutable(){
			return is_executable($this->path);
		}

		/**
		 * @param $newName
		 * @return bool
		 */
		protected function _rename($newName){
			return rename($this->path,$newName);
		}

		/**
		 * @param $destination
		 * @return bool
		 */
		protected function _copy($destination){
			return copy($this->path,$destination);
		}


		protected function _pathinfo(){
			return pathinfo($this->path);
		}

	}
}

