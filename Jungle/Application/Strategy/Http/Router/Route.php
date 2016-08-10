<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.04.2016
 * Time: 22:40
 */
namespace Jungle\Application\Strategy\Http\Router {

	use Jungle\Util\Specifications\Http\RequestInterface;
	use Jungle\Util\Value\String;

	/**
	 * Class Route
	 * @package Jungle\Application\Router
	 */
	class Route extends \Jungle\Application\Router\Route{


		/** @var   */
		protected $_preferred_sub_domain;

		/** @var   */
		protected $_preferred_scheme;// http https

		/** @var   */
		protected $_preferred_method;// GET POST PUT DELETE PATCH HEAD

		/** @var   */
		protected $_preferred_content_type; // application/json application/javascript text/javascript

		/** @var   */
		protected $_preferred_params;// {"param":{VALUE}, "param2":{VALUE}}

		/**
		 * opera, firefox, chrome, safari, amigo, yandex-browser
		 * webkit, mozila, opera, gecko
		 * @var   */
		protected $_preferred_browser;

		/**
		 * @param null $method
		 * @return $this
		 */
		public function setPreferredMethod($method = null){
			$this->_preferred_method = $method;
			return $this;
		}

		/**
		 * @param null $scheme
		 * @return $this
		 */
		public function setPreferredScheme($scheme = null){
			$this->_preferred_scheme = $scheme;
		}

		/**
		 * @param null $subDomain
		 * @return $this
		 */
		public function setPreferredSubDomain($subDomain = null){
			$this->_preferred_sub_domain = $subDomain;
		}

		/**
		 * @param null $type
		 * @return $this
		 */
		public function setPreferredContentType($type = null){
			$this->_preferred_content_type = $type;
			return $this;
		}

		/**
		 * @param array|null $params
		 * @return $this
		 */
		public function setPreferredParams(array $params = null){
			$this->_preferred_params = $params;
			return $this;
		}

		/**
		 * @param null $browser_family
		 * @return $this
		 */
		public function setPreferredBrowser($browser_family = null){
			$this->_preferred_browser = $browser_family;
			return $this;
		}

		/**
		 * @return bool
		 */
		protected function _beforeMatch(){

			if(parent::_beforeMatch()===false){
				return false;
			}

			/** @var RequestInterface $request */
			$request = $this->routing->getRequest();

			if($this->_preferred_scheme && !String::match($request->getScheme(),$this->_preferred_scheme,true)){
				return false;
			}
			if($this->_preferred_browser && !String::match($request->getBrowser()->getName(),$this->_preferred_browser,true)){
				return false;
			}
			if($this->_preferred_method && !String::match($request->getMethod(),$this->_preferred_method,true)){
				return false;
			}
			if($this->_preferred_content_type && !String::match($request->getContentType(),$this->_preferred_content_type,true)){
				return false;
			}
			if($this->_preferred_sub_domain && !String::match(strstr($request->getServer()->getHost(),'.',true),$this->_preferred_sub_domain,true)){
				return false;
			}

			return true;
		}


		/**
		 * @param $reference
		 * @param array $specials destination
		 * @return array
		 */
		protected function _importReference($reference,array $specials = []){
			$specials = parent::_importReference($reference,$specials);
			if(isset($specials['controller']) && strpos($specials['controller'],'.')){
				$ch = explode('.',$specials['controller']);
				$specials['controller'] = array_pop($ch);
				$specials['namespace'] = implode('.',$ch);
			}
			return $specials;
		}



		/**
		 * @param array $specials origin
		 * @return mixed
		 */
		protected function _extractReference(array & $specials){
			$reference = parent::_extractReference($specials);
			if(isset($specials['namespace']) && $specials['namespace']){
				$reference['controller'] = $specials['namespace'] . '.' . $reference['controller'];
			}
			return $reference;
		}



	}
}

