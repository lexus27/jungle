<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 04.03.2016
 * Time: 12:10
 */
namespace Jungle\Util\Smart\Keyword\Storage {

	use Jungle\Util\Smart\Keyword\Keyword;
	use Jungle\Util\Smart\Keyword\Storage;

	/**
	 * Class ArrayFile
	 * @package Jungle\Util\Smart\Keyword\Storage
	 */
	class ArrayFile extends Files{

		/**
		 * @return string
		 */
		public function getExtension(){
			return '.key.php';
		}
		/**
		 * @param Keyword $key
		 * @return string
		 */
		protected function prepareSave(Keyword $key){
			return 'return '.var_export($key->toArray(),true).';';
		}

		/**
		 * @param $keyData
		 * @return Keyword
		 */
		protected function prepareLoaded($keyData){
			return $keyData;
		}

		/**
		 * @param $path
		 * @return Keyword
		 */
		protected function loadFile($path){
			$data = require $path;
			return Keyword::instanceFromData($data);
		}

	}
}

