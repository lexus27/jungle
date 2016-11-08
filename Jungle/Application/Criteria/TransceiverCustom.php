<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.11.2016
 * Time: 20:37
 */
namespace Jungle\Application\Criteria {
	
	class TransceiverCustom implements TransceiverInterface{

		/**
		 * @var callable
		 */
		private $receive;
		/**
		 * @var callable
		 */
		private $link_page;

		public function __construct(callable $receive,callable $link_page){
			$this->receive = $receive;
			$this->link_page = $link_page;
		}

		/**
		 * @param $scopeKey
		 * @param Criteria $criteria
		 */
		public function receiveTo($scopeKey, Criteria $criteria){
			call_user_func($this->receive,$scopeKey, $criteria);
		}

		/**
		 * @param $scopeKey
		 * @param $index
		 * @param Scroller $scroller
		 * @return string
		 */
		public function linkPage($scopeKey, $index, Scroller $scroller){
			return call_user_func($this->link_page, $scopeKey, $index, $scroller);
		}
	}
}

