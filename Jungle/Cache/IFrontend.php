<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 23.02.2016
 * Time: 11:00
 */
namespace Jungle\Cache {

	/**
	 * Interface IFrontend
	 * @package Jungle\Cache
	 */
	interface IFrontend{

		/**
		 * Returns the cache lifetime
		 *
		 * @return int
		 */
		public function getLifetime();

		/**
		 * Check whether if frontend is buffering output
		 *
		 * @return bool
		 */
		public function isBuffering();

		/**
		 * Starts the frontend
		 */
		public function start();

		/**
		 * Returns output cached content
		 *
		 * @return string
		 */
		public function getContent();

		/**
		 * Stops the frontend
		 */
		public function stop();

		/**
		 * Serializes data before storing it
		 *
		 * @param mixed $data
		 */
		public function beforeStore($data);

		/**
		 * Unserializes data after retrieving it
		 *
		 * @param mixed $data
		 */
		public function afterRetrieve($data);

	}
}

