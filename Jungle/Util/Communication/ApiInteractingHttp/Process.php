<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 21:23
 */
namespace Jungle\Util\Communication\ApiInteractingHttp {

	use Jungle\Util\Communication\HttpClient\Request;
	use Jungle\Util\Communication\HttpClient\Response;

	/**
	 * Class Process
	 * @package Jungle\Util\Communication\ApiInteractingHttp
	 */
	class Process extends \Jungle\Util\Communication\ApiInteracting\Process{

		/** @var  Action */
		protected $action;

		/** @var  Request */
		protected $request;

		/** @var  Response */
		protected $response;


		public function __construct(Action $action){
			$this->action = $action;
		}

		/**
		 * @return int|mixed
		 */
		public function getCode(){
			return $this->getResponse()->getCode();
		}

		/**
		 * @param Request $request
		 * @return $this
		 */
		public function setRequest(Request $request){
			$this->request = $request;
			return $this;
		}

		/**
		 * @return Request
		 */
		public function getRequest(){
			return $this->request;
		}

		/**
		 * @return Response|\Jungle\Util\Communication\HttpFoundation\ResponseInterface
		 */
		public function getResponse(){
			return $this->result?: $this->result = $this->request->getResponse();
		}

		/**
		 * @return Action
		 */
		public function getAction(){
			return $this->action;
		}



	}
}

