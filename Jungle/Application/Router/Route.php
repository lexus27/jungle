<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.04.2016
 * Time: 21:41
 */
namespace Jungle\Application\Router {

	use Jungle\Application\Dispatcher;
	use Jungle\Application\Dispatcher\Reference;
	use Jungle\Application\Router\Exception\GenerateLink;
	use Jungle\Application\RouterInterface;
	use Jungle\RegExp\Template;

	/**
	 * Class Route
	 * @package Jungle\Application\Router
	 */
	class Route implements RouteInterface{

		/**
		 * Псевдоним маршрута
		 * @var  string
		 */
		protected $name;

		/**
		 * Маршрутизатор
		 * @var \Jungle\Application\RouterInterface
		 */
		protected $router;

		protected $default_param_name;

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
		 * @var BindingInterface[]
		 */
		protected $bindings = [];

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


		/** @var bool  */
		protected $modifyPath = true;


		/**
		 * @param bool|true $modifyAllowed
		 * @return $this
		 */
		public function setModifyPath($modifyAllowed = true){
			$this->modifyPath = $modifyAllowed;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isModifyPath(){
			return $this->modifyPath;
		}

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
		 * @param \Jungle\Application\RouterInterface $router
		 * @return $this
		 */
		public function setRouter(RouterInterface $router){
			$this->router = $router;
			return $this;
		}

		/**
		 * @return \Jungle\Application\RouterInterface
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
		 * @param $parameter
		 * @return $this
		 */
		public function setDefaultParam($parameter){
			$this->default_param_name = $parameter;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getDefaultParam(){
			return $this->default_param_name;
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
		 * Генерирование запроса соответствующего этому Маршруту
		 * @param $params
		 * @param $reference
		 * @param $preferred_request
		 */
		public function generateRequest($params, $reference, $preferred_request){}

		/**
		 * @param null $params
		 * @param null $reference
		 * @param array|null $specials
		 * @return bool|string
		 */
		public function tryLink($params = null, $reference = null, array $specials = null){
			$tpl = $this->_compilePattern();
			$reference = Reference::normalize($reference, $this->default_reference);

			$specials = (array)$specials;
			$specials = $this->_importReference($reference, $specials);

			if(!is_null($params) && !is_array($params) && $this->default_param_name){
				$params = [
					$this->default_param_name => $params
				];
			}

			$params = (array)$params;
			$params = $this->prepareRenderBindParams($params);
			$template_params = $this->_compositeSpecials($specials, $params);
			// validate supplied parameters by placeholders
			$existing_placeholder_names = [];
			$placeholders = $tpl->getPlaceholders();
			if($placeholders){
				foreach($placeholders as $placeholder){
					$name = $placeholder->getName();
					$existing_placeholder_names[] = $name;
					if(!isset($template_params[$name])){
						if(!$placeholder->isOptional()){
							/** Если обязательного параметра нету в наборе представленных параметров */
							return false;
						}
					}elseif(!$placeholder->getType()->validate($template_params[$name])){
						/** Если значение параметра, не ожидаемо */
						return false;
					}
				}
			}else{
				/** Если в шаблоне не представленны Плейсхолдеры */
				if($this->default_reference){
					foreach($this->default_reference as $key => $value){
						if($reference[$key] !== $value){
							/** Если ссылка по умолчанию, не соответствует переданной ссылке */
							return false;
						}
					}
				}
			}

			// check supplier parameters support in placeholders
			foreach($params as $key => $value){
				if(!in_array($key, $existing_placeholder_names,true)){
					return false;
				}
			}

			try{
				return $tpl->render($params);
			}catch(Template\Exception $e){
				return false;
			}
		}

		/**
		 * @param array $params
		 * @param null $reference
		 * @param array|null $specials
		 * @return string
		 * @throws GenerateLink
		 */
		public function generateLink($params = null, $reference = null, array $specials = null){
			$tpl = $this->_compilePattern();
			$reference = Reference::normalize($reference, $this->default_reference);

			$specials = (array)$specials;
			$specials = $this->_importReference($reference, $specials);

			if(!is_null($params) && !is_array($params) && $this->default_param_name){
				$params = [
					$this->default_param_name => $params
				];
			}

			$params = (array)$params;
			$params = $this->prepareRenderBindParams($params);
			$template_params = $this->_compositeSpecials($specials, $params);
			// validate supplied parameters by placeholders
			$existing_placeholder_names = [];
			$placeholders = $tpl->getPlaceholders();
			if($placeholders){
				foreach($placeholders as $placeholder){
					$name = $placeholder->getName();
					$existing_placeholder_names[] = $name;
					if(!isset($template_params[$name])){
						if(!$placeholder->isOptional()){
							/** Если обязательного параметра нету в наборе представленных параметров */
							throw new GenerateLink('Error generate link: parameter required "'.$name.'"');
						}
					}elseif(!$placeholder->getType()->validate($template_params[$name])){
						/** Если значение параметра, не ожидаемо */
						throw new GenerateLink('Error generate link: Invalid parameter type "' . $name . '"!');
					}
				}
			}else{
				/** Если в шаблоне не представленны Плейсхолдеры */
				if($this->default_reference){
					foreach($this->default_reference as $key => $value){
						if($reference[$key] !== $value){
							/** Если ссылка по умолчанию, не соответствует переданной ссылке */
							throw new GenerateLink('Error generate link: default reference is not equal!');
						}
					}
				}
			}

			// check supplier parameters support in placeholders
			foreach($params as $key => $value){
				if(!in_array($key, $existing_placeholder_names,true)){
					throw new GenerateLink('Param key "'.$key.'" It does not match the host template parameters!');
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
				if($this->_beforeMatched($routing,$reference)!==false){
					$this->matched($params, $reference);
				}
			}
			$this->routing = null;
		}

		protected function _beforeMatched(RoutingInterface $routing,$reference){
			return $routing->getRouter()->beforeRouteMatched($this,$reference,$routing);
		}

		/**
		 * @param array|null $params
		 * @param null $reference
		 * @throws Exception\MatchedException
		 */
		protected function matched(array $params, $reference){
			$routing = $this->routing;
			$this->routing = null;
			$routing->matched($this, function(RoutingInterface $routing) use($params){
				return $this->_normalizeParams($params,$routing);
			}, $reference, true);
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
		 * @param RoutingInterface $routing
		 * @return mixed
		 */
		protected function _normalizeParams(array $params, RoutingInterface $routing){
			$params = array_replace($this->default_params, $params);
			if($this->converter){
				$params = call_user_func($this->converter, $params, $routing->getRequest(), $routing);
			}
			return $this->prepareMatchedBindParams($params);
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
			foreach($reference as $k => $v){
				if(!is_null($v)){
					$specials[$k] = $v;
				}
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
			$path = $this->routing->getRequest()->getPath();
			if($this->modifyPath){
				$path = $this->modifyPath($path);
			}
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
		 * @return Template\Manager
		 */
		protected function getTemplateManager(){
			return $this->router->getTemplateManager();
		}

		/**
		 * @param string $path
		 * @return string
		 */
		public function modifyPath($path){
			return $this->router->modifyPath($path);
		}



		/**
		 * @param callable $converter
		 * @return $this
		 */
		public function setConverter(callable $converter = null){
			$this->converter = $converter;
			return $this;
		}

		/**
		 * @param $parameter
		 * @param callable $decomposite - callable( $value )        { return [$paramKey => $paramValue]; }
		 * @param callable $composite   - callable( array $params ) { return $value; }
		 * @return $this
		 */
		public function bind($parameter,callable $decomposite,callable $composite){
			$this->bindings[$parameter] = new Binding($decomposite,$composite);
			return $this;
		}

		/**
		 * @param $parameter
		 * @param BindingInterface $binding
		 * @return $this
		 */
		public function addBinding($parameter, BindingInterface $binding){
			$this->bindings[$parameter] = $binding;
			return $this;
		}

		/**
		 * @param array $bindings
		 * @param bool|true $merge
		 * @return $this
		 */
		public function setBindings(array $bindings, $merge = false){
			if(!$merge){
				$this->bindings = [];
			}
			foreach($bindings as $key => $binding){
				if($binding instanceof BindingInterface){
					$this->addBinding($key,$binding);
				}elseif(is_array($binding)){
					$this->bind($key, isset($binding[0])?$binding[0]:null,isset($binding[1])?$binding[1]:null);
				}
			}
			return $this;
		}

		/**
		 * Нужно проверить параметры
		 * @param array $params
		 * @return array
		 */
		public function prepareRenderBindParams(array $params){
			foreach($this->bindings as $paramKey => $binding){
				if(array_key_exists($paramKey,$params)){
					$elements = $binding->decomposite($params[$paramKey]);
					unset($params[$paramKey]);
					foreach($elements as $k=>$v){
						$params[$k] = $v;
					}
				}
			}
			return $params;
		}

		/**
		 * @param array $params
		 * @return array
		 */
		public function prepareMatchedBindParams(array $params){
			foreach($this->bindings as $paramKey => $binding){
				$params[$paramKey] = $binding->composite($params);
				$params = $binding->afterComposite($params);
			}
			return $params;
		}


		public function getConverter(){
			return $this->converter;
		}
	}
}

