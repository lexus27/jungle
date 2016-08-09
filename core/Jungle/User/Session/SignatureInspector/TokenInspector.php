<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.08.2016
 * Time: 18:27
 */
namespace Jungle\User\Session\SignatureInspector {
	
	use Jungle\User\Session\SignatureInspector;

	/**
	 * Class TokenInspector
	 * @package Jungle\User\Session\SignatureInspector
	 */
	class TokenInspector extends SignatureInspector{

		/** @var  string */
		protected $param_name;

		/**
		 * @return mixed
		 */
		public function getSignature(){
			$request = $this->request;
			if($request->isPost()){
				return $request->getParam($this->param_name,null,null);
			}
			return null;
		}

		/**
		 * @param $signature
		 * @param $lifetime
		 * @return $this
		 */
		public function setSignature($signature, $lifetime = null){
			$view = $this->view;
			$data = $view->getVar('global_data');
			if(!is_array($data)){
				$data = [];
			}
			$data['accessToken']        = $signature;
			$data['accessTokenExpires'] = time() + $lifetime;
			$view->setVar('global_data', $data);
			return $this;
		}

		/**
		 * @return string
		 */
		public function generateSignature(){
			return uniqid('tok',true);
		}
	}
}

