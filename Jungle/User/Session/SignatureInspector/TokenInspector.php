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
		protected $inspect_key;

		/** @var  string|null  */
		protected $inject_key;

		/** @var  string|null  */
		protected $inject_expires_key;

		/**
		 * TokenInspector constructor.
		 * @param string $inspectKey
		 * @param string|null $injectKey
		 * @param string|null $injectExpiresKey
		 */
		public function __construct($inspectKey, $injectKey = null, $injectExpiresKey = null){
			$this->inspect_key          = $inspectKey;
			$this->inject_key           = $injectKey;
			$this->inject_expires_key   = $injectExpiresKey;
		}

		/**
		 * @param $key
		 * @return $this
		 */
		public function setInjectKey($key){
			$this->inject_key = $key;
			return $this;
		}

		/**
		 * @param $key
		 * @return $this
		 */
		public function setInjectExpiresKey($key){
			$this->inject_expires_key = $key;
			return $this;
		}

		/**
		 * @param $key
		 * @return $this
		 */
		public function setInspectKey($key){
			$this->inspect_key = $key;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function hasSignal(){
			// TODO: Implement hasSignal() method.
		}

		/**
		 * @return mixed
		 */
		public function getSignature(){
			$request = $this->request;
			if($request->isPost()){
				return $request->getParam($this->inspect_key,null,null);
			}
			return null;
		}

		/**
		 * @param $signature
		 * @param $lifetime
		 * @return $this
		 */
		public function setSignature($signature, $lifetime = null){
			if($this->inject_key){
				$view = $this->view;
				$data = $view->getVar('global_data');
				if(!is_array($data)){
					$data = [];
				}
				$data[$this->inject_key] = $signature;
				if($this->inject_expires_key){
					$data[$this->inject_expires_key] = $lifetime?time() + $lifetime:null;
				}
				$view->setVar('global_data', $data);
			}
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

