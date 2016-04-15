<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 29.03.2016
 * Time: 7:47
 */
namespace Jungle\Event {

	use Jungle\Event;

	/**
	 * Interface ListenerAggregateInterface
	 * @package Jungle\Event\ListenerInterface
	 */
	interface ListenerAggregateInterface{

		/**
		 * @return ListenerInterface[]
		 */
		public function & getEventListeners();

	}
}

