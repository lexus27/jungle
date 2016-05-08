<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.04.2016
 * Time: 2:19
 */

/**
 * Запрос
 * Interface RequestInterface
 */
interface RequestInterface{

}

/**
 * Interface HTTPRequestInterface
 */
interface HTTPRequestInterface extends RequestInterface{

	public function getMethod();

	public function getSchema();

	public function getHostname();

	public function getPort();

	public function getUri();

	public function getAuth();

	public function getParam($key);

	public function getContent();

	public function getContentType();

	public function getTime();

	/**
	 * @return HeaderContainerInterface
	 */
	public function getHeaderContainer();

	/**
	 * @return CookieContainerInterface
	 */
	public function getCookieContainer();

	/**
	 * @return ClientInterface
	 */
	public function getClient();
}

interface HeaderContainerInterface{

	public function get($name);

	public function has($name);

	public function set($name, $header);

	public function remove($name);
}

interface CookieInterface{

	public function getName();

	public function setName($name);

	public function getExpires();

	public function setExpires($expires);

	public function getHostname();

	public function setHostname($hostname);

	public function isHttpOnly();

	public function setHttpOnly($only = false);
}

interface CookieContainerInterface{

	public function get($name);

	public function has($name);

	public function set($name, CookieInterface $cookie);

	public function remove($name);
}

/**
 * Interface ClientInterface
 */
interface ClientInterface{

	public function getHostname();

	public function getIpAddress();

	public function getAcceptEncoding();

	public function getAcceptLanguage();

	/**
	 * @return BrowserInterface
	 */
	public function getBrowser();

}

/**
 * Interface BrowserInterface
 */
interface BrowserInterface{

	public function getName();

	public function getEngine();

	public function getVersion();

	public function getOperationSystem();

}
/**
 * Ответ
 * Interface ResponseInterface
 */
interface ResponseInterface{

}

interface HTTPResponseInterface{

	public function setHeader($key, $value);

	public function getHeader($key);

	public function hasHeader($key);

	public function setContent($content);

	public function setContentType($type);

	public function setContentDisposition($disposition);

	public function setCookie($name, $value, $expires, $host, $httpOnly);

	public function getCookie($name);

	public function hasCookie($name);

}

/**
 * Диспетчер контроллеров
 * Interface DispatcherInterface
 */
interface DispatcherInterface{

	public function recognizeRequest(RequestInterface $request);

	public function run($controller, $options);

}

/**
 * Маршрутизщатор
 * Interface RouterInterface
 */
interface RouterInterface{

	public function match(RequestInterface $request);

}

/**
 * Маршрут
 * Interface RouteInterface
 */
interface RouteInterface{

	public function match(RequestInterface $request);

}

/**
 * Interface ControllerInterface
 */
interface ControllerInterface{

	public function execute();

}