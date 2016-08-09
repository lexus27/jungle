<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 14.07.2016
 * Time: 16:38
 */
namespace Jungle\Application {

	use Jungle\Di;

	/**
	 * Class Strategy
	 * @package Jungle\Application\Dispatcher
	 */
	abstract class Strategy extends Di implements StrategyInterface{

		/** @var  string */
		protected $type;

		/** @var  callable|null */
		protected $complete_callback;

		/**
		 * Custom constructor.
		 * @param null $requestChecker
		 * @param callable|null $completeCallback
		 */
		public function __construct($requestChecker = null,callable $completeCallback = null){
			if($completeCallback){
				$this->setCompleteCallback($completeCallback);
			}
		}

		/**
		 * @return string
		 */
		public function getType(){
			return $this->type;
		}

		/**
		 * @param callable $callback
		 * @return $this
		 */
		public function setCompleteCallback(callable $callback){
			$this->complete_callback = $callback;
			return $this;
		}

		/**
		 * @param ResponseInterface $response
		 * @param ViewInterface $view
		 */
		public function complete(ResponseInterface $response, ViewInterface $view){
			if($this->complete_callback){
				call_user_func($this->complete_callback, $response, $this);
			}
		}

	}
}

