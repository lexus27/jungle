<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.11.2016
 * Time: 13:38
 */
namespace Jungle\Application\Criteria {

	/**
	 * Interface CollectionDistributorWorkshopInterface
	 * @package Jungle\Application\Criteria
	 */
	interface CollectionDistributorWorkshopInterface{

		public function setLimit($limit);
		public function setOffset($offset);

		public function setCondition(array $condition);
		public function addCondition(array $condition, $operator = 'AND');

		public function setOrder(array $sort_fields);
		public function prependOrder($field_name, $direction = 'ASC');
		public function appendOrder($field_name, $direction = 'ASC');

	}
}

