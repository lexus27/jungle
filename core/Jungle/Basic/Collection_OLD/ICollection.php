<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.11.2015
 * Time: 12:58
 */
namespace Jungle\Basic\Collection_OLD {

	/**
	 * Interface ICollection
	 * @package Jungle\Basic\Collection
	 *
	 */
	interface ICollection{

		/**
		 * Получить элементы в виде массива
		 * @return array
		 */
		public function toArray();

		/**
		 * Пустая коллекция
		 * @return mixed
		 */
		public function clear();


		/**
		 * @param callable $collector
		 * @param bool $asArray Представить @array собраных элементов, иначе @ICollection
		 * @param bool $subtract , Вычесть собраные элементы из текущей коллекции (Filter)
		 * @return array|ICollection
		 */
		public function collectBy(callable $collector, $asArray = true, $subtract = false);

		/**
		 * @param callable $filter
		 * @return $this
		 */
		public function filterBy(callable $filter);

		/**
		 * @param callable $sorter
		 * @return mixed
		 */
		public function sortBy(callable $sorter);


		/**
		 * Вычесть элементы которые есть в переданных коллекциях
		 * @param callable $compare
		 * @param ICollection $collection
		 * @param ICollection ...
		 * @param bool|false $subtractReturn
		 * @return mixed
		 */
		public function collectionSubtract(
			callable $compare,
			ICollection $collection,
			$subtractReturn = false
		);

		/**
		 * @return mixed
		 */
		public function collectionSubtractArray();

		/**
		 * @return mixed
		 */
		public function collectionDifference();

		/**
		 * @return mixed
		 */
		public function collectionIntersect();


		/**
		 * Получить расхождение коллекций
		 * @param array[]|ICollection[] $collections
		 * @param callable $compareFn compare checker this by passed items
		 * @param bool $returnArray
		 * @return array|ICollection
		 */
		public function difference(array $collections, callable $compareFn = null, $returnArray = false);

		/**
		 * Получить коллекцию
		 * @param array[]|ICollection[] $collections
		 * @param bool $returnArray
		 * @param callable $compareFn function($itemInThis,$itemInPassed){return $itemInThis === $itemInPassed;}
		 * @return array|ICollection
		 */
		public function intersect(
			array $collections,
			$returnArray = false,
			callable $compareFn = null
		);


		/**
		 * @param ICollection , ICollection , ICollection
		 * @return $this
		 */
		public function infusion(...$collections);




		/**
		 * @param $item
		 * @return mixed
		 */
		public function add($item);

		/**
		 * @param $item
		 * @return mixed
		 */
		public function search($item);

		/**
		 * @param $item
		 * @return mixed
		 */
		public function remove($item);

		/**
		 * @param callable $mapper
		 * @return mixed
		 */
		public function each(callable $mapper);

		/**
		 * @return mixed
		 */
		public function __clone();

	}
}

