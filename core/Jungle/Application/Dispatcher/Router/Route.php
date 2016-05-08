<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.04.2016
 * Time: 21:41
 */
namespace Jungle\Application\Dispatcher\Router {

	use Jungle\Application\Dispatcher\Context\External;
	use Jungle\Application\Dispatcher\RouteInterface;
	use Jungle\Application\Dispatcher\Router\Exception\GenerateLink;
	use Jungle\Application\Dispatcher\RouterInterface;
	use Jungle\Application\RequestInterface;
	use Jungle\RegExp\Template;
	use Jungle\Util\Value\Massive;

	/**
	 * Class Route
	 * @package Jungle\Application\Dispatcher\Router
	 */
	class Route implements RouteInterface{

		/**
		 * Псевдоним маршрута
		 * @var  string
		 */
		protected $name;

		/**
		 * Маршрутизатор
		 * @var \Jungle\Application\Dispatcher\RouterInterface
		 */
		protected $router;

		/**
		 * Приоритет в Маршрутизаторе
		 * @var  float
		 */
		protected $priority = 0.0;

		/**
		 * Шаблон
		 * @var  string
		 */
		protected $pattern;

		/**
		 * Настройки шаблона
		 * @var  array
		 */
		protected $pattern_options = [];

		/**
		 * Подготовленый шаблон
		 * @var  null|Template|mixed
		 */
		protected $pattern_compiled = null;

		/**
		 * Событийный вызов замыкания
		 * @var  callable
		 */
		protected $before_match;

		/**
		 * Конвертирование найденых параметров
		 * @var  callable
		 */
		protected $converter;

		/**
		 * Умная Привязка данных окружения к параметрам
		 * @var array
		 */
		protected $binding = [];

		/**
		 * Параметры по умолчанию
		 * @var array
		 */
		protected $default_params = [];

		/**
		 * Ссылка по умолчанию
		 * @var mixed
		 */
		protected $default_reference;


		/**
		 * Текущий процес маршрутизации
		 * @see match
		 * @var  Routing
		 */
		protected $routing;











		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param \Jungle\Application\Dispatcher\RouterInterface $router
		 * @return $this
		 */
		public function setRouter(RouterInterface $router){
			$this->router = $router;
			return $this;
		}

		/**
		 * @return \Jungle\Application\Dispatcher\RouterInterface
		 */
		public function getRouter(){
			return $this->router;
		}

		/**
		 * @return string
		 */
		public function getPattern(){
			return $this->pattern;
		}

		/**
		 * @return array
		 */
		public function getPatternOptions(){
			return $this->pattern_options;
		}

		/**
		 * @return string
		 */
		public function getSpecialParameterPrefix(){
			return $this->router->getSpecialParamPrefix();
		}

		/**
		 * @param callable $beforeMatch
		 * @return $this
		 */
		public function beforeMatch(callable $beforeMatch = null){
			$this->before_match = $beforeMatch;
			return $this;
		}

		/**
		 * @param callable $converter
		 * @return $this
		 */
		public function setConverter(callable $converter){
			$this->converter = $converter;
			return $this;
		}

		/**
		 * @param string $prepare_parameter_key
		 * @param string|callable|array $context
		 * @return $this
		 */
		public function bind($prepare_parameter_key, $context){
			$this->binding[$prepare_parameter_key] = $context;
			ksort($this->binding);
			return $this;
		}

		/**
		 * @param $pattern
		 * @param $options
		 * @return $this
		 */
		public function setPattern($pattern,array $options = []){
			$this->pattern 			= $pattern;
			$this->pattern_options 	= $options;
			return $this;
		}

		/**
		 * @param $priority
		 * @return $this
		 */
		public function setPriority($priority){
			$this->priority = floatval($priority);
			return $this;
		}

		/**
		 * @param array $params
		 * @return $this
		 */
		public function setDefaultParams(array $params = []){
			$this->default_params = $params;
			return $this;
		}

		/**
		 * @return array
		 */
		public function getDefaultParams(){
			return $this->default_params;
		}

		/**
		 * @param mixed $reference
		 * @return $this
		 */
		public function setDefaultReference($reference = null){
			$this->default_reference = $reference;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getDefaultReference(){
			return $this->default_reference;
		}

		/**
		 * @return bool
		 */
		public function isDynamic(){
			$template = $this->_compilePattern();
			if($template){
				$prefixed = $this->prefixSpecialsKeys(['module', 'controller', 'action']);
				foreach($template->getPlaceholders() as $placeholder){
					if(in_array($placeholder->getName(), $prefixed,true)){
						return true;
					}
				}
				return false;
			}else{
				return false;
			}
		}


		/**
		 * @return array
		 */
		public function getDynamics(){
			$a = [];
			$template = $this->_compilePattern();
			if($template){
				$prefixed = $this->prefixSpecialsKeys(['module', 'controller', 'action']);
				foreach($template->getPlaceholders() as $placeholder){
					$name = $placeholder->getName();
					if(in_array($name, $prefixed,true)){
						$a[] = substr($name,1);
					}
				}
			}
			return $a;
		}

		/**
		 * HTTP specific
		 */
		public function isDirectVisible(){

		}


		/**
		 * HTTP specific
		 * Генерирование запроса соответствующего этому Маршруту
		 * @param $params
		 * @param $reference
		 * @param $preferred_request
		 */
		public function generateRequest($params, $reference, $preferred_request){}

		/**
		 * @param array $params
		 * @param null $reference
		 * @param array|null $specials
		 * @return string
		 * @throws GenerateLink
		 */
		public function generateLink(array $params = null,$reference = null,array $specials = null){
			$tpl = $this->_compilePattern();
			$specials = $this->_importReference($reference, (array)$specials);
			$params = $this->_compositeSpecials($specials, (array)$params);
			foreach($tpl->getPlaceholders() as $ph){
				$name = $ph->getName();
				if(!isset($params[$name]) || $params[$name]===null){
					if(!$ph->isOptional()){
						throw new GenerateLink('Error generate link: parameter required "'.$name.'"');
					}
				}elseif(!$ph->getType()->isValid($params[$name])){
					throw new GenerateLink('Error generate link: Invalid parameter type "' . $name . '"!');
				}
			}
			try{
				return $tpl->render($params);
			}catch(Template\Exception $e){
				throw new GenerateLink('Error generate link: '.$e->getMessage().'.',1,$e);
			}
		}


		/**
		 * @param RoutingInterface $routing
		 * @return void
		 */
		public function match(RoutingInterface $routing){
			$this->routing = $routing;
			if(($this->_beforeMatch()!==false) && ($params = $this->_matchPattern())!==false){
				$specials = $this->_extractSpecials($params);
				$reference = $this->_extractReference($specials);
				$this->matched($params, $reference);
			}
			$this->routing = null;
		}

		/**
		 * @param array|null $params
		 * @param null $reference
		 * @throws Exception\MatchedException
		 */
		protected function matched(array $params, $reference){
			$params = $this->_normalizeParams($params);
			$routing = $this->routing;
			$this->routing = null;
			$routing->matched($this, $params, $reference, true);
		}

		/**
		 * @return bool
		 */
		protected function _beforeMatch(){
			if($this->before_match && call_user_func($this->before_match,$this->routing) === false){
				return false;
			}
			return true;
		}

		/**
		 * @param array $params
		 * @return mixed
		 */
		protected function _normalizeParams(array $params){
			$params = array_replace($this->default_params, $params);
			if($this->converter){
				$params = call_user_func($this->converter, $params, $this->routing);
			}
			if($this->binding){
				foreach($this->binding as $parameterKey => $contextAccessing){
					$value = $this->_contextAccess($contextAccessing, $params);
					Massive::setNestedValue($params, $parameterKey, $value, '.');
				}
			}
			return $params;
		}


		/**
		 * @param array $params
		 * @return array
		 */
		protected function _extractSpecials(array & $params){
			$prefix  = $this->getSpecialParameterPrefix();
			$prefix_length 	= strlen($prefix);
			$specials = [];
			foreach($params as $param_name => $value){
				if(substr($param_name,0,$prefix_length) === $prefix){
					$specials[substr($param_name,$prefix_length)] = $value;
				}else{
					$params[$param_name] = $value;
				}
			}
			return $specials;
		}


		/**
		 * @param array $specials origin
		 * @return mixed
		 */
		protected function _extractReference(array & $specials){
			$a = [
				'module' 	=> isset($this->default_reference['module'])?$this->default_reference['module']:null,
				'controller'=> isset($this->default_reference['controller'])?$this->default_reference['controller']:null,
				'action' 	=> isset($this->default_reference['action'])?$this->default_reference['action']:null
			];
			foreach($a as $k => $v){
				if(isset($specials[$k])){
					$a[$k] = $specials[$k];
					unset($specials[$k]);
				}
			}
			return $a;
		}

		/**
		 * @param $reference
		 * @param array $specials destination
		 * @return array
		 */
		protected function _importReference($reference,array $specials = []){
			if(!is_array($reference)){
				$reference = [];
			}

			if(!isset($reference['module'])){
				$specials['module'] = isset($this->default_reference['module'])?$this->default_reference['module']:null;
			}

			if(!isset($reference['controller'])){
				$specials['controller'] = isset($this->default_reference['controller'])?$this->default_reference['controller']:null;
			}

			if(!isset($reference['action'])){
				$specials['action'] = isset($this->default_reference['action'])?$this->default_reference['action']:null;
			}
			return $specials;
		}

		/**
		 * @param array $specials
		 * @param array $params
		 * @return array
		 */
		protected function _compositeSpecials(array $specials,array $params = []){
			$prefix = $this->getSpecialParameterPrefix();
			foreach($specials as $param => $value){
				$params[$prefix . $param] = $value;
			}
			return $params;
		}

		/**
		 * @param array $keys
		 * @return array prefixed
		 */
		protected function prefixSpecialsKeys(array $keys){
			$prefix = $this->getSpecialParameterPrefix();
			foreach($keys as $i => $key){
				$keys[$i] = $prefix . $key;
			}
			return $keys;
		}


		/**
		 * @return array|bool
		 */
		protected function _matchPattern(){
			$this->_compilePattern();
			$path = $this->normalizePath($this->routing->getRequest());
			return $this->pattern_compiled->match($path);
		}

		/**
		 * @return Template|mixed|null
		 */
		protected function _compilePattern(){
			if(!$this->pattern_compiled){
				$phOpts 	= isset($this->pattern_options['placeholders'])?$this->pattern_options['placeholders']:null;
				$tplOpts 	= isset($this->pattern_options['pattern'])?$this->pattern_options['pattern']:null;
				$this->pattern_compiled = $this->getTemplateManager()->template($this->pattern, $phOpts, $tplOpts);
			}
			return $this->pattern_compiled;
		}

		/**
		 * @param $context_target_definition
		 * @param $params
		 * @return mixed
		 */
		protected function _contextAccess($context_target_definition, $params){
			if(is_callable($context_target_definition)){
				return call_user_func($context_target_definition, $this->routing);
			}elseif(is_array($context_target_definition)){
				if(!isset($context_target_definition['type'])){
					return null;
				}
				if($context_target_definition['type'] === 'model'){
					$model 			= $context_target_definition['model'];
					$param 			= $context_target_definition['param'];
					$field			= $context_target_definition['field'];
					if(isset($params[$param])){
						$o = new \stdClass();
						$o->{$field} = $params[$param];
						return $o;
					}else{

					}
				}
				return null;
			}else{
				return $context_target_definition;
			}
		}

		/**
		 * @return Template\Manager
		 */
		protected function getTemplateManager(){
			return $this->router->getTemplateManager();
		}

		/**
		 * @param RequestInterface $request
		 * @return string
		 */
		public function normalizePath(RequestInterface $request){
			return $this->router->normalizePath($request->getPath());
		}

	}
}

