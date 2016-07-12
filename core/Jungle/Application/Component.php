<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.07.2016
 * Time: 23:24
 */
namespace Jungle\Application {
	
	use Jungle\Di\DiInterface;
	use Jungle\Di\InjectionAwareInterface;

	/**
	 * Class Component
	 * @package Jungle\Application
	 */
	abstract class Component implements InjectionAwareInterface{

		/** @var DiInterface */
		protected $_di;

		/**
		 * @return DiInterface
		 */
		public function getDi(){
			return $this->_di;
		}

		/**
		 * @param DiInterface $di
		 * @return $this
		 */
		public function setDi(DiInterface $di){
			$this->_di = $di;
			return $this;
		}
	}
}

