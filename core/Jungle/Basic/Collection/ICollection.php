<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 20.11.2015
 * Time: 16:55
 */
namespace Jungle\Basic\Collection{

	/**
	 * Interface ICollection
	 * @package Jungle\Basic
	 */
	interface ICollection{

		/**
		 * @return $this
		 */
		public function clear();

		/**
		 * @return array
		 */
		public function toArray();

		/**
		 * @param callable $collector
		 * @param bool|true $asArray
		 * @param bool $autoFilter
		 * @return array
		 */
		public function collectBy(callable $collector, $asArray = true, $autoFilter = false);

		/**
		 * @param callable $filter
		 * @param bool $asArray
		 * @return array
		 */
		public function filterBy(callable $filter, $asArray = true);

		/**
		 * @param callable $mapper
		 * @return $this
		 */
		public function each(callable $mapper);

	}
}

