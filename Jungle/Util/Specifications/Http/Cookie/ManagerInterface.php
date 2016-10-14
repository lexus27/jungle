<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.06.2016
 * Time: 15:39
 */
namespace Jungle\Util\Specifications\Http\Cookie {

	use Jungle\Util\Specifications\Http\RequestInterface;
	use Jungle\Util\Specifications\Http\ResponseInterface;

	/**
	 * Interface ManagerInterface
	 * @package Jungle\Util\Specifications\Http
	 */
	interface ManagerInterface extends ConfigurationInterface{

		/**
		 * @param $name
		 * @return mixed
		 */
		public function getCookie($name);

		/**
		 * @param $key
		 * @param $value
		 * @param null $expire
		 * @param null $path
		 * @param null $secure
		 * @param null $httpOnly
		 * @param null $domain
		 * @return mixed
		 */
		public function setCookie($key, $value = null, $expire = null, $path = null, $secure = null, $httpOnly = null, $domain = null);

		/**
		 * @param $name
		 * @return mixed
		 */
		public function removeCookie($name);

		/**
		 * @param RequestInterface $request
		 * @return mixed
		 */
		public function setRequest(RequestInterface $request);

		/**
		 * @return mixed
		 */
		public function getRequest();

		/**
		 * @param ResponseInterface $response
		 * @return mixed
		 */
		public function setResponse(ResponseInterface $response);

		/**
		 * @return mixed
		 */
		public function getResponse();

		/**
		 * @return mixed
		 */
		public function isSent();

	}
}

