<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.08.2016
 * Time: 8:15
 */
namespace Jungle\User\OldSession\SignatureProvider {
	
	use Jungle\User\OldSession\SignatureProvider;
	use Jungle\Util\Communication\Http\Response;
	use Jungle\Util\Specifications\Http\RequestInterface;

	/**
	 * Class HttpParamProvider
	 * @package Jungle\User\OldSession\SignatureProvider
	 */
	class HttpParamProvider extends SignatureProvider{

		/** @var string */
		protected $type = 'query';

		/** @var string */
		protected $param_name = 'accessKey';

		/**
		 * @return mixed
		 */
		public function getSignature(){
			$this->request->getPost();
		}

		/**
		 * @param $signature
		 * @return $this
		 */
		public function setSignature($signature){
			// TODO: Implement setSignature() method.
		}

		/**
		 * @return $this
		 */
		public function removeSignature(){
			// TODO: Implement removeSignature() method.
		}

		/**
		 * @param $type
		 * @param $key
		 * @param RequestInterface $request
		 * @return mixed|null
		 */
		protected function _getParamValue($type, $key, RequestInterface $request){
			switch($type){
				case 'get':     return $request->getQuery($key);
				case 'post':    return $request->getPost($key);
				case 'header':  return $request->getHeader($key);
				case 'param':   return $request->getParam($key);
			}
			return null;
		}

		protected function _injectParamValue($type, $key, Response $response){
			switch($type){
				case 'get':     return $response->set($key);
				case 'post':    return $response->getPost($key);
				case 'header':  return $response->getHeader($key);
				case 'param':   return $response->getParam($key);
			}
			return null;
		}
	}
}

