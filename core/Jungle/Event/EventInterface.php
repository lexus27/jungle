<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.04.2016
 * Time: 13:07
 */
namespace Jungle\Event {

	use Jungle\Basic\INamed;

	/**
	 * Interface EventInterface
	 * @package Jungle\Event
	 */
	interface EventInterface extends INamed{

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name);

		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @return bool
		 */
		public function isCancelable();

		/**
		 * @return bool
		 */
		public function isStoppable();

		/**
		 * @return bool
		 */
		public function isCollector();

		/**
		 * @return bool
		 */
		public function isCollectorDynamic();

		/**
		 * @return bool
		 */
		public function isActionChangeAllowed();

		/**
		 * @return bool
		 */
		public function isDataReferencesAllowed();

		/**
		 * @param null|string $key
		 * @return \stdClass
		 */
		public function getMemory($key = null);

		/**
		 * @param ListenerAggregateInterface $initiator
		 * @param array|null $data
		 * @param callable|null $action
		 * @return EventProcess
		 */
		public function invoke(ListenerAggregateInterface $initiator, array $data = null, callable $action = null);




	}
}

