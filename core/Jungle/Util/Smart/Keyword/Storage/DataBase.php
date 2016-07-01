<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 25.01.2016
 * Time: 16:37
 */
namespace Jungle\Util\Smart\Keyword\Storage {

	use Jungle\Util\Smart\Keyword\Keyword;
	use Jungle\Util\Smart\Keyword\Storage;

	/**
	 * Class DataBase
	 * @package Jungle\Util\Smart\Keyword\Storage
	 */
	class DataBase extends Storage{

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
		 * @param null $matcher
		 * @return array identifiers
		 */
		public function getList($matcher=null){
			// TODO: Implement getList() method.
		}

		/**
		 * @TODO $matcher CLASS MATCHER
		 * @param $matcher
		 * @return mixed
		 */
		public function getCount($matcher = null){
			// TODO: Implement getCount() method.
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

