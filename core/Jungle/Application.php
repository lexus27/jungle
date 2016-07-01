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
	use Jungle\Di\DiInterface;
	use Jungle\Di\InjectionAwareInterface;

	/**
	 * Class Application
	 * @package Jungle
	 */
	class Application implements InjectionAwareInterface{

		/** @var  \ReflectionObject */
		protected $reflection;

		/** @var  Dispatcher */
		protected $dispatcher;

		/** @var  Loader */
		protected $loader;

		/** @var  DiInterface */
		protected $dependency_injector;

		/** @var  Application */
		protected static $latest_application;

		/**
		 * @return mixed
		 */
		public static function app(){
			return self::$latest_application;
		}

		/**
		 * @param Application $application
		 */
		public static function setDefault(Application $application){
			self::$latest_application = $application;
		}

		/**
		 * Application constructor.
		 */
		public function __construct(){
			$this->reflection = new \ReflectionObject($this);
			self::$latest_application = $this;
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
		 * @return Loader
		 */
		public function getLoader(){
			return $this->loader;
		}

		public function handle(){

		}

		/**
		 * @param DiInterface $di
		 * @return $this
		 */
		public function setDi(DiInterface $di){
			$this->dependency_injector = $di;
			return $this;
		}

		/**
		 * @return DiInterface
		 */
		public function getDi(){
			return $this->dependency_injector;
		}




	}
}

