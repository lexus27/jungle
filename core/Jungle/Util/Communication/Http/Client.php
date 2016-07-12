<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.07.2016
 * Time: 17:56
 */
namespace Jungle\Util\Communication\Http {
	
	use Jungle\Util\Specifications\Http\ClientInterface;
	use Jungle\Util\Specifications\Http\ClientSettableInterface;
	use Jungle\Util\Specifications\Http\ProxyInterface;

	/**
	 * Class Client
	 * @package Jungle\Util\Communication\Http
	 */
	class Client implements ClientInterface, ClientSettableInterface{

		/** @var array  */
		protected $languages = [];

		/**
		 * @return string
		 */
		public function getIp(){
			return $_SERVER['SERVER_ADDR'];
		}

		/**
		 * @return int
		 */
		public function getPort(){
			return $_SERVER['SERVER_PORT'];
		}

		/**
		 * @return string
		 */
		public function getHost(){
			return $_SERVER['SERVER_PORT'];
		}

		/**
		 * @param array|null $languages
		 */
		public function setLanguages(array $languages){
			$this->languages = $languages;
		}

		/**
		 * @return string
		 */
		public function getBestLanguage(){
			return isset($this->languages[0])?$this->languages[0]:locale_get_default();
		}

		/**
		 * @return string[]
		 */
		public function getLanguages(){
			return $this->languages?:[locale_get_default()];
		}

		/**
		 * @param ProxyInterface $proxy
		 * @return mixed
		 */
		public function setProxy(ProxyInterface $proxy){
			// TODO: Implement setProxy() method.
		}

		/**
		 * @return bool
		 */
		public function isProxied(){
			// TODO: Implement isProxied() method.
		}

		/**
		 * @return ProxyInterface
		 */
		public function getProxy(){
			// TODO: Implement getProxy() method.
		}
	}
}

