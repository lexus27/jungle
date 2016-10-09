<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 16:10
 */
namespace Jungle\Util\Communication\Integrator {

	/**
	 * Class Process
	 * @package Jungle\Util\Communication\Integrator
	 */
	class Process implements ProcessInterface{

		/** @var  MethodInterface */
		protected $method;

		/** @var  bool */
		protected $canceled = false;

		/**
		 * Process constructor.
		 * @param MethodInterface|null $method
		 */
		public function __construct(MethodInterface $method = null){
			$this->method = $method;
		}



		/**
		 * @return MethodInterface
		 */
		public function getMethod(){
			return $this->method;
		}

		/**
		 * @return mixed
		 */
		public function isCanceled(){
			// TODO: Implement isCanceled() method.
		}

	}
}

