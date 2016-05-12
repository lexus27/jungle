<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.05.2016
 * Time: 16:05
 */

include(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'loader.php');



class Request implements Jungle\Application\RequestInterface,\Jungle\HTTPFoundation\RequestInterface{

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
		// TODO: Implement getHostname() method.
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
		// TODO: Implement getParameter() method.
	}

	/**
	 * @param $parameter
	 * @return bool
	 */
	public function hasParameter($parameter){
		// TODO: Implement hasParameter() method.
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
	 * @return \Jungle\HTTPFoundation\ClientInterface
	 */
	public function getClient(){
		// TODO: Implement getClient() method.
	}

	/**
	 * @return \Jungle\HTTPFoundation\ContentInterface
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
class RequestSet implements IteratorAggregate{

	/** @var \Jungle\Application\RequestInterface[]  */
	protected $requests = [];

	/**
	 * @param \Jungle\Application\RequestInterface $request
	 * @return $this
	 */
	public function addRequest(\Jungle\Application\RequestInterface $request){
		$this->requests[] = $request;
		return $this;
	}

	/**
	 * @return \Jungle\Application\RequestInterface[]
	 */
	public function getIterator(){
		return new \ArrayIterator($this->requests);
	}

}
$reqSet = new RequestSet();
$reqSet->addRequest(new Request('/user/76/view'));
$reqSet->addRequest(new Request('/76'));
$reqSet->addRequest(new Request('/kutuz27'));
$reqSet->addRequest(new Request('/lamp/on','GET'));



$router = new \Jungle\Application\Dispatcher\Router\HTTP\Router();


$router->get('/lamp/{turn:bool(on,off)(,)}',[
	'controller' 	=> 'lamp',
	'action' 		=> 'turn'
])->setName('Включение лампы');

$router->any('/user/{id:int.nozero}{$action?</|>:word}',[
	'controller' => 'user'
])->setName('user');

$router->any('/{user:word}',[
	'controller' 	=> 'user',
	'action' 		=> 'profile'
])
		->setName('short-user-name')
		->bind('user',[
			'type' 	=> 'model',
			'model' => 'App\Model\User',
			'param' => 'user',
			'field' => 'username'
		]);

$router->any('/{user:int.nozero}',[
	'controller' 	=> 'user',
	'action' 		=> 'profile'
])
		->setName('short-user-id')
		->bind('user',[
				'type' 	=> 'model',
				'model' => 'App\Model\User',
				'param' => 'user',
				'field' => 'id'
		]);

foreach($reqSet as $request){
	if($router->match($request)){
		echo '<pre>'.$router->lastMatchedRoute()->getName().' ';
		var_dump($router->lastMatchedRouteResult());
		echo '</pre>';
	}
}

echo '<pre>',print_r($router->generateLink('lamp','turn',[
		'turn' => true
]), 1),'</pre>';
echo '<pre>',print_r($router->generateLink('user','profile',[
	'user' => 1
]), 1),'</pre>';
echo '<pre>ss ',print_r($router->generateLink('user','profile',[
	'fx' => 1
]), 1),'</pre>';
echo '<pre>',print_r($router->generateLink('user','profile',[
	'id' => 1
]), 1),'</pre>';
echo '<pre>',print_r($router->generateLink('lamp','turn',[
		'turn' => true
]), 1),'</pre>';


/**
echo $routes[2]->generateLink([
	'controller' 	=> 'hello',
	'action'		=> 'test',
	'params' 		=> [
		'user.id' => 678
	]
]);
 * */