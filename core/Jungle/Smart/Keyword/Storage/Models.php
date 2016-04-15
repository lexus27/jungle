<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 25.01.2016
 * Time: 16:37
 */
namespace Jungle\Smart\Keyword\Storage {

	use Jungle\Smart\Keyword\Keyword;
	use Jungle\Smart\Keyword\Storage;

	/**
	 * Class Models
	 * @package Jungle\Smart\Keyword\Storage
	 */
	class Models extends Storage{

		/**
		 * @param Keyword $key
		 */
		public function save(Keyword $key){
			// TODO: Implement save() method.
		}

		/**
		 * @param $identifier
		 * @return Keyword
		 */
		public function load($identifier){
			// TODO: Implement load() method.
		}

		/**
		 * @return array identifiers
		 */
		public function getList(){
			// TODO: Implement getList() method.
		}

		/**
		 * @param $identifier
		 * @return bool
		 */
		public function has($identifier){
			// TODO: Implement has() method.
		}

		/**
		 * @param $identifier
		 * @return bool
		 */
		public function remove($identifier){
			// TODO: Implement removeNode() method.
		}
	}
}

