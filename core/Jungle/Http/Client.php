<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.06.2016
 * Time: 0:12
 */
namespace Jungle\Http {

	use Jungle\Http\Current\Request;
	use Jungle\Util\BrowserInterface;
	use Jungle\Util\OperationSystemInterface;
	use Jungle\Util\Specifications\Http\ClientInterface;

	/**
	 * Class Client
	 * @package Jungle\HTTPFoundation
	 */
	class Client implements ClientInterface{

		/** @var  string */
		protected $ip;

		/** @var  int */
		protected $port;

		/** @var  BrowserInterface */
		protected $browser;

		/** @var  OperationSystemInterface */
		protected $operation_system;

		/** @var  string[] */
		protected $languages = [];

		/** @var  Request */
		protected $request;

		public function setRequest(Request $request){
			$this->request = $request;
			return $this;
		}

		public function getRequest(){
			return $this->request;
		}

		/**
		 * @return string
		 */
		public function getIpAddress(){
			return $this->request->getClientIp();
		}

		/**
		 * @return mixed
		 */
		public function getPort(){
			return $this->request->getClientPort();
		}

		/**
		 * @return BrowserInterface
		 */
		public function getBrowser(){
			return $this->browser;
		}

		/**
		 * @return OperationSystemInterface
		 */
		public function getOperationSystem(){
			return $this->operation_system;
		}

		/**
		 * @return string
		 */
		public function getBestLanguage(){
			return !empty($this->languages)?$this->languages[0]:'en';
		}

		/**
		 * @return string[]
		 */
		public function getLanguages(){
			return $this->languages;
		}


	}
}

