<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.11.2016
 * Time: 13:36
 */
namespace Jungle\Application\Criteria {

	/**
	 * Interface CollectionDistributorInterface
	 * @package Jungle\Application\Criteria
	 */
	interface CollectionDistributorInterface{

		/**
		 * @return mixed
		 */
		public function getTotalCount();

		/**
		 * @return mixed
		 */
		public function getCount();

		/**
		 * @return mixed
		 */
		public function getCollection();

	}
}

