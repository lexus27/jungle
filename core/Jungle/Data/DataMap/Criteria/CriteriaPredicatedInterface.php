<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 25.03.2016
 * Time: 15:53
 */
namespace Jungle\Data\DataMap\Criteria {

	/**
	 * Interface CriteriaPredicatedInterface
	 * @package Jungle\Data\DataMap\Criteria
	 */
	interface CriteriaPredicatedInterface extends CriteriaInterface{

		/**
		 * @return array
		 */
		public function getPredicates();

	}
}

