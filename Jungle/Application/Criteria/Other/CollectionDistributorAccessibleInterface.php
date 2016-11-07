<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.11.2016
 * Time: 13:41
 */
namespace Jungle\Application\Criteria {

	/**
	 * Interface CollectionDistributorAccessibleInterface
	 * @package Jungle\Application\Criteria
	 */
	interface CollectionDistributorAccessibleInterface{

		/**
		 * @param $capture
		 * @return mixed
		 */
		public function setCaptureDenied($capture = true);

		/**
		 * @return bool
		 */
		public function isCaptureDenied();

		/**
		 * @return array
		 */
		public function getDeniedCollection();

		/**
		 * @return mixed
		 */
		public function getDeniedCount();

		/**
		 * @return array
		 */
		public function getAnyCollection();

		/**
		 * @return int
		 */
		public function getAnyCount();

		/**
		 * @param $item
		 * @return bool
		 */
		public function isDeniedItem($item);

	}
}

