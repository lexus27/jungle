<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.04.2016
 * Time: 21:39
 */
namespace Jungle\Application\Dispatcher {

	use Jungle\Application\Dispatcher\Router\Exception\GenerateLink;
	use Jungle\Application\Dispatcher\Router\Exception\MatchedException;
	use Jungle\Application\Dispatcher\Router\Routing;
	use Jungle\Application\RequestInterface;
	use Jungle\RegExp\Template;
	use Jungle\Util\Value\String;

	/**
	 * Class Router
	 * @package Jungle\Application
	 */
	abstract class Router implements RouterInterface{

		/**
		 * @Experimental
		 * @var callable|null
		 */
		protected $path_converter;

		/** @var  bool */
		protected $remove_extra_caseless = false;

		/** @var  string|string[]|null */
		protected $remove_extra_left;

		/** @var  string|string[]|null */
		protected $remove_extra_right;


		/** @var  RouteInterface[]  */
		protected $routes = [];

		/** @var array */
		protected $notFoundConfig = [null,null];

		/** @var  Template\Manager */
		protected $template_manager;

		/** @var string  */
		protected $special_params_prefix = '$';

		/** @var  Routing|null */
		protected $last_success;

		/**
		 * @param string|string[]|null $subject
		 * @return $this
		 */
		public function removeExtraLeft($subject){
			$this->remove_extra_left = $subject;
			return $this;
		}

		/**
		 * @param string|string[]|null $subject
		 * @return $this
		 */
		public function removeExtraRight($subject){
			$this->remove_extra_right = $subject;
			return $this;
		}

		/**
		 * @param bool $caseless
		 * @return $this
		 */
		public function removeExtraCaseless($caseless = true){
			$this->remove_extra_caseless = $caseless;
			return $this;
		}

		/**
		 * @param $path
		 * @return string
		 */
		public function modifyPath($path){
			return String::trimWordsSides($path, $this->remove_extra_left, $this->remove_extra_right, $this->remove_extra_caseless);
		}


		/**
		 * @param RouteInterface $route
		 */
		public function addRoute(RouteInterface $route){
			$this->routes[] = $route;
		}

		/**
		 * @param \Jungle\Application\Dispatcher\RouteInterface $route
		 * @return $this
		 */
		public function removeRoute(RouteInterface $route){
			$i = array_search($route, $this->routes , true);
			if($i !== false){
				array_splice($this->routes , $i ,1);
			}
			return $this;
		}

		/**
		 * @param $reference
		 * @param array $params
		 * @return $this
		 */
		public function notFound($reference, array $params){
			$this->notFoundConfig = [$reference, $params];
			return $this;
		}

		/**
		 * @return Routing|null
		 */
		public function getLastMatched(){
			return $this->last_matched;
		}

		/**
		 *
		 * Генерирование ссылки на действие контроллера
		 * {Module}.{Controller}.{Action}
		 * {Controller}.{Action} > DefaultModule
		 * {Action} > Default Module and Controller
		 * #{Anonymous_Action} - Анонимное действие
		 *
		 * @param array $params
		 * @param $reference
		 * @return null|string
		 * @throws GenerateLink
		 */
		public function generateLink(array $params = null, $reference = null){
			foreach($this->routes as $route){
				try{
					return $route->generateLink($params,$reference);
				}catch(GenerateLink $e){}
			}
			throw new GenerateLink('Not Found suitable route for passed arguments!');

		}

		/**
		 * @param $route_alias
		 * @param array $params
		 * @param null $reference
		 * @return string
		 * @throws GenerateLink
		 */
		public function generateLinkBy($route_alias, array $params = null, $reference = null){
			foreach($this->routes as $route){
				if($route->getName() === $route_alias){
					return $route->generateLink((array)$params, $reference);
				}
			}
			throw new GenerateLink('Not Found suitable route by name "'.$route_alias.'"!');
		}



		/**
		 * @param RequestInterface $request
		 * @return Routing
		 */
		public function match(RequestInterface $request){
			$routing = new Routing($request,$this);
			foreach($this->routes as $route){
				try{
					$route->match($routing);
				}catch(MatchedException $e){
					return $e->getResult();
				}
			}
			list($reference, $params) = $this->notFoundConfig;
			return $routing->notFound($params,$reference,false);
		}



		/**
		 * @param Template\Manager $manager
		 * @return $this
		 */
		public function setTemplateManager(Template\Manager $manager){
			$this->template_manager = $manager;
			return $this;
		}

		/**
		 * @return Template\Manager
		 */
		public function getTemplateManager(){
			if(!$this->template_manager){
				$this->template_manager = new Template\Manager();
				$this->_initializeTemplateManager($this->template_manager);
			}
			return $this->template_manager;
		}


		/**
		 * @return string
		 */
		public function getSpecialParamPrefix(){
			return $this->special_params_prefix;
		}

		/**
		 * @param $prefix
		 */
		public function setSpecialParamPrefix($prefix){
			$this->special_params_prefix = $prefix;
		}

		/**
		 * @param $param
		 * @param array $defaults
		 * @return $this
		 */
		public function setSpecialParamDefaults($param, array $defaults){
			$templateManager = $this->getTemplateManager();
			$templateManager->setPlaceholderDefaults($this->special_params_prefix . $param , $defaults);
			return $this;
		}

		/**
		 * @param array $defaults
		 * @return $this
		 */
		public function setTemplateDefaults(array $defaults){
			$templateManager = $this->getTemplateManager();
			$templateManager->setTemplateDefaults($defaults);
			return $this;
		}

		/**
		 * @param Template\Manager $mgr
		 */
		protected function _initializeTemplateManager(Template\Manager $mgr){
			$mgr->setPlaceholderDefaults($this->special_params_prefix . 'module',[
					'pattern' => '[[:alpha:]][\w\-]*\w'
			]);
			$mgr->setPlaceholderDefaults($this->special_params_prefix . 'controller',[
					'pattern' => '[[:alpha:]][\w\-]*\w',
					'renderer' => function($value){
						return String::uncamelize($value,'-');
					},
					'evaluator' => function($value){
						return String::camelize($value,['-','_'],false);
					}
			]);
			$mgr->setPlaceholderDefaults($this->special_params_prefix . 'action',[
					'pattern' => '[[:alpha:]][\w\-]*\w',
					'evaluator' => function($value){
						return str_replace('-','_',$value);
					}
			]);
		}


	}
}

