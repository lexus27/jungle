<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.03.2016
 * Time: 0:05
 */
namespace Jungle\Event {

	use Jungle\Event;

	/**
	 * Class ListenerInterface
	 * @package Jungle\Util
	 */
	abstract class Listener implements ListenerInterface{

		/** @var bool  */
		protected $_permanent = false;

		/** @var float */
		protected $_priority  = .0;

		/**
		 * @return bool
		 */
		public function isPermanent(){
			return $this->_permanent;
		}

		/**
		 * @param bool|true $permanent
		 * @return $this
		 */
		public function setPermanent($permanent = true){
			$this->_permanent = boolval($permanent);
			return $this;
		}


		/**
		 * @return int
		 */
		public function getPriority(){
			return $this->_priority;
		}

		/**
		 * @param float $priority
		 * @return $this
		 */
		public function setPriority($priority = .0){
			$this->_priority = floatval($priority);
			return $this;
		}

	}
}

