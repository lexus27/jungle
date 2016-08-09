<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 31.01.2016
 * Time: 22:47
 */
namespace Jungle\FileSystem\OldModel\Manager {

	use Jungle\FileSystem\OldModel;

	/**
	 * Class Factory
	 * @package Jungle\FileSystem\OldModel\Manager
	 */
	class Factory{

		/**
		 * @param $name
		 * @param $mimeType
		 * @return OldModel
		 */
		public function createByMimeType($name, $mimeType){

		}

		/**
		 * @param $name
		 * @param $isExisting
		 * @return Model\Directory
		 */
		public function createDirectory($name, $isExisting = false){
			return new Model\Directory($name, $isExisting);
		}

		/**
		 * @param $name
		 * @param bool $isExisting
		 * @return Model\File
		 */
		public function createFile($name, $isExisting = false){
			return new Model\File($name, $isExisting);
		}

	}
}

