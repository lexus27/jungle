<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.03.2016
 * Time: 14:50
 */
namespace Jungle\Event {

	/**
	 * Interface ObservableInterface
	 * @package Jungle\Event\ObservableInterface
	 */
	interface ObservableInterface extends ListenerAggregateInterface{


		/**
		 * @param ListenerInterface|callable $listener
		 * @return $this
		 */
		public function attachEventListener(callable $listener);

		/**
		 * @param ListenerInterface|callable $listener
		 * @return int|bool
		 */
		public function searchEventListener(callable $listener);

		/**
		 * @param ListenerInterface|callable $listener
		 * @return $this
		 */
		public function detachEventListener(callable $listener);



	}
}

