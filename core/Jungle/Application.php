<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.05.2016
 * Time: 17:13
 */
namespace Jungle {
	
	use Jungle\Application\Dispatcher;

	/**
	 * Class Application
	 * @package Jungle
	 */
	class Application{

		/** @var  \ReflectionObject */
		protected $reflection;

		/** @var  Dispatcher */
		protected $dispatcher;

		/** @var  Loader */
		protected $loader;


		public function __construct(){
			$this->reflection = new \ReflectionObject($this);
		}

		/**
		 * @param Dispatcher $dispatcher
		 * @return $this
		 */
		public function setDispatcher(Dispatcher $dispatcher){
			$this->dispatcher = $dispatcher;
			return $this;
		}

		/**
		 * @return Dispatcher
		 */
		public function getDispatcher(){
			return $this->dispatcher;
		}

		/**
		 * @return mixed
		 */
		public function getLoader(){
			return $this->loader;
		}

		public function handle(){

		}




	}
}

