<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.11.2016
 * Time: 15:29
 */
namespace Jungle\Application\Criteria {


	/**
	 * Interface ScrollerInterface
	 * @package Jungle\Application\Criteria
	 */
	interface ScrollerInterface{

		/**
		 * @param $index
		 * @return mixed
		 */
		public function pageOffset($index);

		/**
		 * @return float
		 */
		public function end();

		/**
		 * @return string
		 */
		public function endIndex();

		/**
		 * @return mixed
		 */
		public function count();

		/**
		 * @return array
		 */
		public function links();

		/**
		 * @param int $skip
		 * @return string|null
		 */
		public function prev($skip = 0);

		/**
		 * @param int $skip
		 * @return string|null
		 */
		public function next($skip = 0);


		/**
		 * @param int $skip
		 * @return float|null
		 */
		public function prevIndex($skip = 0);

		/**
		 * @param int $skip
		 * @return float|null
		 */
		public function nextIndex($skip = 0);

		/**
		 * @param $start
		 * @param $amount
		 * @return array
		 */
		public function sliceLinks($start, $amount);

		/**
		 * @return mixed
		 */
		public function countBefore();

		/**
		 * @return mixed
		 */
		public function countAfter();

		/**
		 * @param int $count
		 * @return array
		 */
		public function linksBefore($count = 1);

		/**
		 * @param int $count
		 * @return array
		 */
		public function linksAfter($count = 1);



		/**
		 * @param $pageIndex
		 * @return bool
		 */
		public function link($pageIndex);

		public function current();


	}
}

