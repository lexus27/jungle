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
use Jungle\Util\Value\Massive;

require 'loader.php';


/**
 * Alternate
 */

/**
 * @Example User Current
 * @id = {authorized}
 */
$router_name    = 'user.profile';

$pattern    = '/profile';// Pattern is constant
$path       = '/profile';

$pattern    = '/user/profile'; // Pattern is constant
$path       = '/user/profile';


/**
 * @Example User
 * @id = 17
 */
$router_name    = 'user';

$pattern    = '/{id:int:(\d+)}';
$path       = '/17';          // simplify

$pattern    = '/{id:int:(\d+)}.user';
$path       = '/17.user';     // simplify with dot

$pattern    = '/id{id:int:(\d+)}';
$path       = '/id17';        // simplify prefixed

$pattern    = '/user/{id:int:(\d+)}';
$path       = '/user/17';     // qualified full path



/**
 * @Example User
 * @id = ivan_petrov
 */
$pattern    = '/{id:string:(\w[\w\d]+)}';
$path       = '/ivan_petrov';         // simplify

$pattern    = '/{id:string:(\w[\w\d]+)}.user';
$path       = '/ivan_petrov.user';    // alternate with dot

$pattern    = '/user/{id:string:(\w[\w\d]+)}';
$path       = '/user/ivan_petrov';    // qualified full path


/**
 * @Example Documentation
 */
$router_name    = 'documentation';

$pattern    = '/help/{language:(\w{1,3})}/{doc_path:string}';
$path       = '/help/en/oo-paradigm';  // language:    'en'

$pattern    = '/help/{doc_path:string}';
$path       = '/help/oo-paradigm';     // language:    Header: Accept-Language

/**
 * @Placeholder
 */


$placeholder_defaults = [
	'name'      => null,
	'type'      => 'string',
	'pattern'   => '@see &.type'
];

/**
 * Parameters
 */

$parameters = [
	'language', // ru, en, fr ...and them
	'extension' , // .php .html .png .css .js ...and them
];

$required_parameters = [
	'module',
	'namespace',
	'controller',
	'action',
];


/**
 * Задачи Маршрутизатора:
 * 1. Обеспечить {Направление} исходя из {Запроса}
 * 2.
 *
 * Маршрутизатор знает:
 * 1. Возможные {Маршруты}
 * 2. {uri}
 * 3. {pattern}
 *
 * Основной состовляющей для поиска является Шаблон Пути {pattern}
 *
 * Запрос имеет {uri} как основное свойство
 * Маршруты имеют свой {pattern} для опознания соответствующего {uri}
 *
 *
 *
 * Контроллер должен получить данные на вход
 *
 * Проверка конфликтов Маршрутов в Маршрутизаторе
 *
 *
 * Генерация путей соответствующих выходу на контроллер посредством Router`а
 *
 * Компонент Router в системе требуемый для определения "Направления" исходя из "Запроса"
 *
 * Говоря языком человека которого окружают в реальной жизни - реальные объекты, Работа Маршрутизатора напоминает
 * работу поликлиники, а именно:
 * Пациент приходит в поликлинику, заходит к доктору и говорит о своей "проблеме",
 * У доктора есть ряд "методов для диагностирования" на основе которых он ставит пациенту "направление", "диагноз" и
 * ряд рекомендаций.
 *
 * Маршрутизатор должен отдать системе-понятное "Направление", будь то реально найденный маршрут или NOT_FOUND действие.
 * Основной причиной когда может потребоваться Маршрутизация , это требование проектирования единой точки входа для
 * "Внешний клиентов"
 * Маршрутизатор помогает нам, разложить "по полочкам" исполняющие обработчики "Контроллеры"
 *
 * Маршрутизатор должен быть хоть-как то оптимизирован на возможность при его помощи генерировать ссылки на
 * контроллеры, по идентификатору контроллера
 *
 * Представим что у нас есть ряд слоев или компонентов в системе, которые не должны тесно между собой связываться .
 *
 * Контроллеры как таковые являются определением "глобальной управляющей логики".
 * Это та точка которая по запросу инициирует выполнение целевой бизнес логики, да и вообще можно считать лицевой
 * стороной приложения где программист создает вызовы других слоев приложения.
 * Как-то себя идентифицируют в каркасе системы.
 * за 1 вызов выполняют 1 определенное действие в системе,
 * могут требовать что-то на вход для поддержания выполнения этой логики,
 * могут формировать или помогают формировать потенциально-отображаемый результат своей работы.
 * Как же связать Маршрутизатор и Контроллер, ведь маршрутизатор отдает всего-лишь направление, но не вызывает
 * контроллер, а связывать их явно и вовсе не логично.
 * Тут нужно добавить диспетчера , который будет уметь вызывать любой контроллер в системе.
 *
 * Диспетчер так-же сможет обеспечить нас возможностью вызова контроллера из контроллера, НО когда мы вызываем
 * контроллер из текущего контроллера, нам не обязательно облагать его на HTTP специфику Ввода-Вывода, то есть не обязательно использовать Маршрутизатор.
 *
 * И так мы выяснили что "Маршрутизатор" является просто прослойкой между "Глобальным клиентом" и "Контроллером" при
 * участии "Диспетчера".
 *
 * Пример:
 *
 * Адрес http://example.site/user/17
 *
 * Какой предполагается результат от внешнего запроса по такому адресу? а никакой из самой ссылки не предполагается:),
 * но по такому адресу можно вывести:
 * HTML страницу специфичную профилю пользователя с id 17 (Обычный рендер страницы , используя View или Шаблонизатор)
 * JSON объект содержащий информацию по пользователю с id 17 (используется как HTTP API)
 * XML описание объекта пользователя с id 17 (используется как HTTP API)
 * Другие варианты
 * На деле мы можем получить по такому адресу данные в любом возможном формате, опираясь всего-лишь на некоторые
 * мелочи, типа тривиальный пример - Заголовок X-Requested-With, обычно благодаря нему определяют что нужен JSON.
 *
 * Представим что http://example.site/user/17 в любом случае приведет нас на 1 конкретный контроллер
 * Мы в этом контроллере можем опознать тип запрашиваемого формата ответа, при этом Целевая логика контроллера не
 * изменилась.
 *
 * Теперь мы можем разделить общую логику такого контроллера на:
 * Получение пользователя с id=17 из базы данных (Целевая логика)
 * Определение формата ответа.
 * Формирование ответа в соответствии с внешними требованиями.
 *
 * Предполагается что контроллер должен иметь возможность быть независимым от всякой специфики запроса (WEB
 * спецификаций) или хотя бы помогать разработчику писать такой код который просто принимает что-то на вход и что-то формирует или помогает формировать на выход.
 *
 * :eek: Представим что у нас есть задача с проверкой HTTP_REFERER на уровне контроллера.
 * :eek: Представим что нам нужно отделить параметры GET от POST на уровне контроллера
 *
 *
 *
 *
 *
 *
 *
 *
 *
 */






$pattern = '/help/{language}/{x:float}/{y:float}/{station}/{*}';
$compliant = '/help/ru/1/2.4/rockford/h';

$domain = 'www.example.com';





interface RequestInterface{

}
interface ResponseInterface{

}

interface HTTPRequestInterface{

}

interface RouterInterface{

}
interface HTTPRouterInterface{

}

interface DispatcherInterface{



}


class StringType{

	protected $_name;

	protected $_pattern;

	protected $_type;

	/**
	 * @param $subject
	 * @return bool
	 */
	public function match($subject){
		return preg_match('~^'.addcslashes($this->_pattern,'~').'$~',$subject)>0;
	}

	/**
	 * @param $subject
	 * @return mixed
	 */
	public function convert($subject){
		if(is_callable($this->_type)){
			$subject = call_user_func($this->_type,$subject);
		}else{
			settype($subject,$this->_type);
		}
		return $subject;
	}


	/**
	 * @param string $pattern
	 * @return $this
	 */
	public function setPattern($pattern){
		$this->_pattern = $pattern;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPattern(){
		return $this->_pattern;
	}

}

/**
 * Class StringTypeManager
 * @package router
 */
class StringTypeManager{

	protected $types = [];

	public function getType($type){
		return Massive::getNamed($this->types,$type,'strcasecmp');
	}

}

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


	public function getUriSlug($index);

	public function getUriSlugCount();


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

/**
 * Interface RouterInterface
 * Маршрутизатор
 */
interface RouterInterface{

	/**
	 * @param RequestInterface $request
	 * @return RouteInterface
	 */
	public function match(RequestInterface $request);

}

interface RouteInterface{

	/**
	 * @param RequestInterface $request
	 * @return mixed
	 */
	public function check(RequestInterface $request);

}

/**
 * Структура Контроллеров пока не определена
 *  * то ли контроллер как callable
 *  * то-ли контроллер как module.controller.action
 *
 * Классовое определенение контроллера дает преимущества:
 *  * Общая инициализация на все действия.
 *  * Содержание приватных под-методов действия
 *  * Единый для всех действий контейнер свойств
 *  * Наследование (Приемущество в абстрагировании)
 *
 * Память Контроллера:
 *  * Память Web Session
 *  * Память User
 *  * Память Triggered Start/Stop Формирование последовательных вызовов контроллеров, типа шагов
 *  * * Вообщем память контроллера весьма специфичное явление
 *
 *
 * Interface ControllerInterface
 * @package router
 */
interface ControllerInterface{

}

/**
 * Главный слой
 * Interface ApplicationInterface
 * @package router
 */
interface ApplicationInterface{


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
	public function check(RequestInterface $request, RouterInterface $parent = null){
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

	protected $_prefix_compiled;

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
	 * @return RouteInterface
	 */
	public function check(RequestInterface $request){
		$uri = $request->getUri();
		if($this->_prefix_compiled===null){

		}
		if($this->_prefix_compiled){
			if(preg_match($this->_prefix_compiled,$uri,$m)){
				$uri = ltrim(substr($uri,0, strlen($m[0])),'/');
				$uri = '/'.$uri;
				$request = new RequestDecorator( $request );
				$request->setUri($uri);
			}else{
				return false;
			}
		}
		foreach($this->routes as $route){
			$matched = $route->check($request);
			if($matched){
				return $matched;
			}
		}
		return false;
	}

	public function getPattern(){
		// TODO: Implement getPattern() method.
	}

	public function setPattern($pattern){
		// TODO: Implement setCustomPattern() method.
	}

	public function getParameters(){
		// TODO: Implement getParameters() method.
	}

	public function setParameters(array $parameters){
		// TODO: Implement setParameters() method.
	}
}
class RequestDecorator implements RequestInterface{

	/** @var  RequestInterface  */
	protected $_request;

	/** @var  string */
	protected $_uri;


	public function __construct(RequestInterface $request){
		$this->_request = $request;
	}

	public function getMethod(){
		return $this->_request->getMethod();
	}

	public function getUri(){
		return $this->_uri!==null?$this->_uri:$this->_request->getUri();
	}

	public function setUri($uri){
		$this->_uri = $uri;
	}

	public function getUriSlug($index){
		return $this->_request->getUriSlug($index);
	}

	public function getUriSlugCount(){
		return $this->_request->getUriSlugCount();
	}

	public function getBody(){
		return $this->_request->getBody();
	}

	public function getRawBody(){
		return $this->_request->getRawBody();
	}

	public function getHeader($header_key){
		return $this->_request->getHeader($header_key);
	}

	public function hasHeader($header_key){
		return $this->_request->hasHeader($header_key);
	}

	public function getRequestedWith(){
		return $this->_request->getRequestedWith();
	}

	public function getPreferLanguage(){
		return $this->_request->getPreferLanguage();
	}

	public function getPreferEncoding(){
		return $this->_request->getPreferEncoding();
	}

	public function getPreferContentType(){
		return $this->_request->getPreferContentType();
	}

	public function getParam($key){
		return $this->_request->getParam($key);
	}

	public function getPost($key){
		return $this->_request->getPost($key);
	}

	public function getClientIp(){
		return $this->_request->getClientIp();
	}

	public function getClientBrowser(){
		return $this->_request->getClientBrowser();
	}

	public function getClientOperationSystem(){
		return $this->_request->getClientOperationSystem();
	}
}

$url = 'http://';
