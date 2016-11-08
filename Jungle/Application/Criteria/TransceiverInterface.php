<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.11.2016
 * Time: 18:12
 */
namespace Jungle\Application\Criteria {

	/**
	 * Class TransceiverInterface
	 * @package Jungle\Application\Criteria
	 */
	interface TransceiverInterface{

		/**
		 * @param $scopeKey
		 * @param Criteria $criteria
		 */
		public function receiveTo($scopeKey, Criteria $criteria);

		/**
		 * @param $scopeKey
		 * @param $index
		 * @param Scroller $scroller
		 * @return string
		 */
		public function linkPage($scopeKey, $index, Scroller $scroller);


	}
}

