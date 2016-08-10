<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.06.2016
 * Time: 0:04
 */
namespace Jungle\Util\Data\Foundation\Condition {

	/**
	 * Interface PredicatedConditionInterface
	 * @package Jungle\Util\Data\Foundation\Condition
	 */
	interface PredicatedConditionInterface{

		/**
		 * @param array $collated_data
		 * @return array
		 */
		public function setPredicatedData(array $collated_data);

		/**
		 * @return array
		 */
		public function getPredicatedData();

	}
}

