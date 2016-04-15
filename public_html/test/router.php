<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 21.03.2016
 * Time: 16:40
 */

namespace router;
require 'loader.php';

$Route = '/path/to/file.png';

$pattern = '/help/{language}/{x:float}/{y:float}/{station}/{*}';
$compliant = '/help/ru/1/2.4/rockford/h';

$domain = 'www.example.com';



function parse($string, $pattern){

	$properties = [];
	$i = 0;
	$pattern = preg_replace_callback('@/\{(\*)?(\w+)?(?:\:(\w+)(?:\((.*?)\))?)?\}@',
	function($matches) use(& $properties, & $i){
		$name       = isset($matches[2])?$matches[2]:null;
		$type       = isset($matches[3]) && $matches[3]?$matches[3]:'string';
		$expression = isset($matches[4])?$matches[4]:null;
		$optional   = isset($matches[1]) && $matches[1];
		if(!$expression){
			switch($type){
				case 'string':
					$expression = '.+';
					break;
				case 'float':
					$expression = '[\d]+(?:\.[\d]+)?';
					break;
				case 'int':
					$expression = '[\d]+?';
					break;
			}
		}
		$expression = $optional?"(?:$expression)?":$expression;
		$expression = $name?"(?<{$name}>{$expression})":"($expression)";
		$i++;
		$properties[$name?$name:$i] = $type;
		return $optional?"(?:/$expression)?":'/'.$expression;
	},$pattern);
	$pattern = '@'.$pattern.'@';
	echo htmlspecialchars($pattern);


	if(preg_match($pattern,$string,$matches)){
		$values = [];
		foreach($properties as $property => $type){
			$value = $matches[$property];
			settype($value,$type);
			$values[$property] = $value;
		}
		echo '<pre>';
		var_dump($values);
	}



}
parse($compliant, $pattern);

/**
 * Interface RequestInterface
 */
interface RequestInterface{

	public function getMethod();

	public function getUri();
	
	public function getBody();

	public function getRawBody();

	public function getHeader($header_key);

	public function hasHeader($header_key);

	public function getRequestedWith();

	public function getPreferLanguage();

	public function getPreferEncoding();

	public function getPreferContentType();


	public function getParam($key);

	public function getPost($key);

	
	public function getClientIp();

	public function getClientBrowser();

	public function getClientOperationSystem();

}

/**
 * Interface ContextInterface
 */
interface ContextInterface{

	/**
	 * @param ContextInterface $context
	 * @return mixed
	 */
	public function setPrevious(ContextInterface $context);

	/**
	 * @return ContextInterface
	 */
	public function getPrevious();


	/**
	 * @param RequestInterface $request
	 * @return mixed
	 */
	public function setRequest(RequestInterface $request);

	/**
	 * @return RequestInterface
	 */
	public function getRequest();


	/**
	 * @param RouteInterface $route
	 * @return mixed
	 */
	public function setRoute(RouteInterface $route);

	/**
	 * @return RouteInterface
	 */
	public function getRoute();


	public function setModuleName($module);

	public function getModuleName();


	public function setControllerName($controller);

	public function getControllerName();


	public function setActionName($action);

	public function getActionName();


}

/**-
 * Interface RouterInterface
 * Маршрутизатор
 */
interface RouterInterface{

	/**
	 * @param RouteInterface $route
	 * @return mixed
	 */
	public function addRoute(RouteInterface $route);

	/**
	 * @param RouteInterface $route
	 * @return mixed
	 */
	public function searchRoute(RouteInterface $route);

	/**
	 * @param RouteInterface $route
	 * @return mixed
	 */
	public function removeRoute(RouteInterface $route);

	/**
	 * @param RequestInterface $request
	 * @return RouteInterface
	 */
	public function match(RequestInterface $request);

	/**
	 * @return RouteInterface
	 */
	public function getForward();

	/**
	 * @return RequestInterface
	 */
	public function getRequest();

}

/**
 * Interface RouteInterface
 * Маршрут
 */
interface RouteInterface{

	public function getPattern();

	public function setPattern($pattern);

	public function getParameters();

	public function setParameters(array $parameters);

	/**
	 * @param RequestInterface $request
	 * @param RouterInterface $parent
	 * @return RouteInterface|false
	 */
	public function match(RequestInterface $request, RouterInterface $parent = null);

}

interface RouteFastHandleInterface{

	public function setHandler(callable $handler);

	public function getHandler();
	
}

interface Dispatcher{
	
}

/**
 * Class Route
 */
class Route implements RouteInterface{

	/** @var  string */
	protected $pattern;
	
	/** @var array  */
	protected $parameters = [];

	/**
	 * @param RequestInterface $request
	 * @param RouterInterface|null $parent
	 * @return RouteInterface|false
	 */
	public function match(RequestInterface $request, RouterInterface $parent = null){
		return false;
	}

	public function getPattern(){
		return $this->pattern;
	}

	public function setPattern($pattern){
		$this->pattern = $pattern;
		return $this;
	}

	public function getParameters(){
		return $this->parameters;
	}

	public function setParameters(array $parameters){
		$this->parameters = $parameters;
	}
}

/**
 * Class RouteGroup
 */
class RouteGroup implements RouteInterface{

	/**
	 * @var string
	 * 
	 * Для префикса пока не ясна реализация, маршрут работает с запросом , логика вычисления инкапсулирована в 
	 * методе match, за основу вычисления берется не урл а запрос, 
	 */
	protected $prefix;

	/** @var  RouteInterface[]  */
	protected $routes = [];

	/**
	 * @param $prefix
	 * @return $this
	 */
	public function setPrefix($prefix){
		$this->prefix = $prefix;
		return $this;
	}

	/**
	 * @param RequestInterface $request
	 * @param RouterInterface $parent
	 * @return RouteInterface
	 */
	public function match(RequestInterface $request, RouterInterface $parent = null){
		foreach($this->routes as $route){

			$route->match($request);

		}
	}

	public function getPattern(){
		// TODO: Implement getPattern() method.
	}

	public function setPattern($pattern){
		// TODO: Implement setPattern() method.
	}

	public function getParameters(){
		// TODO: Implement getParameters() method.
	}

	public function setParameters(array $parameters){
		// TODO: Implement setParameters() method.
	}
}

$url = 'http://';
