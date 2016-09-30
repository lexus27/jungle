<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.05.2016
 * Time: 16:22
 */




include(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'loader.php');



class Request implements Jungle\Application\RequestInterface,\Jungle\Http\RequestInterface{

	protected $path = '/s/s/s';

	public function __construct($path,$method = 'GET'){
		$this->path = $path;
		$this->method = $method;
	}
	/**
	 * @return string
	 */
	public function getPath(){
		return $this->path;
	}

	/**
	 * @return string
	 */
	public function getMethod(){
		return $this->method;
	}

	/**
	 * @return string
	 */
	public function getScheme(){
		return '';
	}

	/**
	 * @return int
	 */
	public function getPort(){
		return '';
	}

	/**
	 * @return string
	 */
	public function getHostname(){
		// TODO: Implement getServerHost() method.
	}

	/**
	 * @return string
	 */
	public function getAuthType(){
		// TODO: Implement getAuthType() method.
	}

	/**
	 * @return string|null
	 */
	public function getAuthLogin(){
		// TODO: Implement getAuthLogin() method.
	}

	/**
	 * @return string|null
	 */
	public function getAuthPassword(){
		// TODO: Implement getAuthPassword() method.
	}

	/**
	 * @return string
	 */
	public function getUri(){
		return $this->getPath();
	}

	/**
	 * @param $parameter
	 * @return mixed
	 */
	public function getParameter($parameter){
		// TODO: Implement getParam() method.
	}

	/**
	 * @param $parameter
	 * @return bool
	 */
	public function hasParameter($parameter){
		// TODO: Implement hasParam() method.
	}

	/**
	 * @return string|null
	 */
	public function getReferrer(){
		// TODO: Implement getReferrer() method.
	}

	/**
	 * @param $headerKey
	 * @return mixed
	 */
	public function getHeader($headerKey){
		// TODO: Implement getHeader() method.
	}

	/**
	 * @param $headerKey
	 * @return bool
	 */
	public function hasHeader($headerKey){
		// TODO: Implement hasHeader() method.
	}

	/**
	 * @return \Jungle\Http\ClientInterface
	 */
	public function getClient(){
		// TODO: Implement getClient() method.
	}

	/**
	 * @return \Jungle\Http\ContentInterface
	 */
	public function getContent(){
		// TODO: Implement getContent() method.
	}

	/**
	 * @return string
	 */
	public function getContentType(){
		// TODO: Implement getContentType() method.
	}
}


$t = microtime(true);

$dispatcher = new \Jungle\Application\Dispatcher();
$dispatcher->setControllerNamespace('App\\Controllers');


$router = new \Jungle\Application\Adaptee\Http\Dispatcher\Router();

for($i=0;$i<1000;$i++){
	$router->post('/user/login',[
		'controller' 	=> 'index',
		'action' 		=> 'index'
	]);
}

$router->any('/user/{id:int.nozero}',[
	'controller' 	=> 'index',
	'action' 		=> 'user'
])->setName('user-login');

$dispatcher->setRouter($router);
$dispatcher->dispatch(new Request('/user/login','POST'));


echo '<br/>'.sprintf('%.4F',microtime(true) - $t);















