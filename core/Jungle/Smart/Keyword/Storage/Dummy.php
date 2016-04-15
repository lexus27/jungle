<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 12.09.2015
 * Time: 18:28
 */
namespace Jungle\Smart\Keyword\Storage {

	use Jungle\Smart\Keyword\Keyword;
	use Jungle\Smart\Keyword\Storage;

	/**
	 * Class Dummy
	 * @package Jungle\Smart\Keyword\Storage
	 */
	class Dummy extends Storage{

		/**
		 *
		 */
		public function __construct(){
			parent::__construct();
		}

		/**
		 * @param Keyword $key
		 */
		public function save(Keyword $key){}

		/**
		 * @param $identifier
		 * @return Keyword
		 */
		public function load($identifier){
			return null;
		}

		/**
		 * @param $identifier
		 * @return bool
		 */
		public function has($identifier){
			return false;
		}

		/**
		 * @param $identifier
		 * @return bool
		 */
		public function remove($identifier){
			return true;
		}

		/**
		 * @param null $matcher
		 * @return array
		 */
		public function getList($matcher = null){
			return [];
		}

		/**
		 * @TODO $matcher CLASS MATCHER
		 * @param $matcher
		 * @return mixed
		 */
		public function getCount($matcher = null){
			return 0;
		}
	}
}

