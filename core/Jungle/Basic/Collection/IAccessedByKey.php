<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 20.11.2015
 * Time: 17:05
 */
namespace Jungle\Basic\Collection {

	/**
	 * Interface IAccessedByKey
	 * @package Jungle\Basic\Collection
	 *
	 * unique key in collection
	 *
	 */
	interface IAccessedByKey{

		/**
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		public function setByKey($key,$value);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getByKey($key);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function removeByKey($key);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasByKey($key);


	}
}

