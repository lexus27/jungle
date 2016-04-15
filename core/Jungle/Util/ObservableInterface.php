<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.03.2016
 * Time: 20:22
 */
namespace Jungle\Util {

	use Jungle\Event\IListener;

	/**
	 * Interface IObservable
	 * @package Jungle\Util
	 */
	interface ObservableInterface{

		/**
		 * @param IListener|array|callable $listener
		 * @return $this
		 */
		public function attachEventListener($listener);

		/**
		 * @param \Jungle\Event\IListener $listener
		 * @return $this
		 */
		public function detachEventListener(IListener $listener);

	}
}

