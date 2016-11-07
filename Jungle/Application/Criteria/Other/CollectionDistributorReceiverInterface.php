<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.11.2016
 * Time: 13:54
 */
namespace Jungle\Application\Criteria {

	/**
	 * Interface CollectionDistributorReceiverInterface
	 * @package Jungle\Application\Criteria
	 */
	interface CollectionDistributorReceiverInterface{

		public function getLimit();

		public function getOffset();

		public function getCondition();

		public function getSortFields();

	}
}

