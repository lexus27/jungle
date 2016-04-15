<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.03.2016
 * Time: 3:20
 */
namespace Jungle\Event {

	use Jungle\Event;
	use Jungle\Event\Listener\ListenerProcess;

	/**
	 * Interface ListenerInterface
	 * @package Jungle\Event\ListenerInterface
	 */
	interface ListenerInterface{

		/**
		 * @param EventInterface $event
		 * @return bool
		 */
		public function isDesiredEvent(EventInterface $event);

		/**
		 * @return bool
		 */
		public function isActive();

		/**
		 * @return bool
		 */
		public function isPermanent();


		/**
		 * @return float
		 */
		public function getPriority();

		/**
		 * @param EventProcess $event
		 * @param ListenerProcess $listener
		 * @return bool|void
		 */
		public function __invoke(ListenerProcess $listener, EventProcess $event);

	}

}

