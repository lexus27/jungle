<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 23.02.2016
 * Time: 11:01
 */
namespace Jungle\Cache {

	/**
	 * Class Backend
	 * @package Jungle\Cache
	 */
	abstract class Backend implements IBackend{

		/**
		 * @var IFrontend
		 */
		protected $frontend;

		/**
		 * @var
		 */
		protected $options;


		/**
		 *
		 * Starts a cache. The keyname allows to identify the created fragment
		 *
		 * @param int|string $keyName
		 * @param int $lifetime
		 * @return mixed
		 */
		public function start($keyName, $lifetime = null){
			// TODO: Implement start() method.
		}

		/**
		 * Stops the frontend without store any cached content
		 * @param boolean $stopBuffer
		 */
		public function stop($stopBuffer = true){
			// TODO: Implement cancel() method.
		}

		/**
		 * Returns front-end instance adapter related to the back-end
		 * @return IFrontend|mixed
		 */
		public function getFrontend(){
			return $this->frontend;
		}

		/**
		 * @param IFrontend $frontend
		 * @return $this
		 */
		public function setFrontend(IFrontend $frontend){
			$this->frontend = $frontend;
			return $this;
		}

		/**
		 * Returns the backend options
		 *
		 * @return array
		 */
		public function getOptions(){
			return $this->options;
		}

		/**
		 * Checks whether the last cache is fresh or cached
		 *
		 * @return bool
		 */
		public function isFresh(){
			// TODO: Implement isFresh() method.
		}

		/**
		 * Checks whether the cache has starting buffering or not
		 *
		 * @return bool
		 */
		public function isStarted(){
			// TODO: Implement isStarted() method.
		}

		/**
		 * Sets the last key used in the cache
		 *
		 * @param string $lastKey
		 */
		public function setLastKey($lastKey){
			// TODO: Implement setLastKey() method.
		}

		/**
		 * Gets the last key stored by the cache
		 *
		 * @return string
		 */
		public function getLastKey(){
			// TODO: Implement getLastKey() method.
		}

		/**
		 * Returns a cached content
		 *
		 * @param int|string $keyName
		 * @param int $lifetime
		 * @return mixed
		 */
		public function get($keyName, $lifetime = null){
			if($lifetime===null){
				$lifetime = $this->frontend->getLifetime();
			}
		}

		/**
		 * Stores cached content into the file backend and stops the frontend
		 *
		 * @param int|string $keyName
		 * @param string $content
		 * @param int $lifetime
		 * @param boolean $stopBuffer
		 */
		public function save($keyName = null, $content = null, $lifetime = null, $stopBuffer = true){
			if($content===null){
				$this->frontend->isBuffering();
			}
		}

		/**
		 * Deletes a value from the cache by its key
		 *
		 * @param int|string $keyName
		 * @return boolean
		 */
		public function delete($keyName){
			// TODO: Implement remove() method.
		}

		/**
		 * Query the existing cached keys
		 *
		 * @param string $prefix
		 * @return array
		 */
		public function queryKeys($prefix = null){
			// TODO: Implement queryKeys() method.
		}

		/**
		 * Checks if cache exists and it hasn't expired
		 *
		 * @param string $keyName
		 * @param int $lifetime
		 * @return boolean
		 */
		public function exists($keyName = null, $lifetime = null){
			// TODO: Implement exists() method.
		}
	}
}

