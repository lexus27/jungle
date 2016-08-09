<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 14.07.2016
 * Time: 23:21
 */
namespace Jungle\Application\Strategy {
	
	use Jungle\Application\RequestInterface;
	use Jungle\Application\Strategy;

	/**
	 * Class Custom
	 * @package Jungle\Application\Strategy
	 */
	class Custom extends Strategy{

		/** @var string|callable */
		protected $request_checker;

		/** @var  string */
		protected $type;


		/**
		 * Custom constructor.
		 * @param null $type
		 * @param null $requestChecker
		 * @param callable|null $completeCallback
		 */
		public function __construct($type = null,$requestChecker = null,callable $completeCallback = null){
			if(!is_null($type)){
				$this->setType($type);
			}
			if(!is_null($requestChecker)){
				if(is_string($requestChecker)){
					$this->setRequestClassname($requestChecker);
				}else{
					$this->setRequestChecker($requestChecker);
				}
			}
			if($completeCallback){
				$this->setCompleteCallback($completeCallback);
			}
		}

		/**
		 * @param $type
		 * @return $this
		 */
		public function setType($type){
			$this->type = $type;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getType(){
			return $this->type;
		}

		/**
		 * @param $checker
		 * @return $this
		 */
		public function setRequestChecker(callable $checker){
			$this->request_checker = $checker;
			return $this;
		}

		/**
		 * @param $classname
		 * @return $this
		 */
		public function setRequestClassname($classname){
			if(!is_string($classname)){
				throw new \LogicException('Classname must be string!');
			}
			$this->request_checker = $classname;
			return $this;
		}

		/**
		 * @param \Jungle\Application\RequestInterface $request
		 * @return bool
		 */
		public function check(RequestInterface $request){
			if($this->request_checker){
				if(is_string($this->request_checker)){
					return is_a($request, $this->request_checker);
				}
				return call_user_func($this->request_checker);
			}
			return false;
		}

	}
}

