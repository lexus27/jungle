<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.07.2016
 * Time: 19:04
 */
namespace Jungle\Application\View {
	
	use Jungle\Application\Dispatcher\ProcessInterface;
	use Jungle\Application\RequestInterface;
	use Jungle\Application\ResponseInterface;
	use Jungle\Application\View\ViewStrategy\RendererRule;
	use Jungle\Application\View\ViewStrategy\RendererRuleInterface;
	use Jungle\Application\ViewInterface;

	/**
	 * Class ViewStrategy
	 * @package Jungle\Application\View
	 */
	class ViewStrategy implements ViewStrategyInterface{

		/** @var RendererRule[]|callable[] */
		protected $rules = [];

		/** @var  mixed[]  */
		protected $variables = [];

		/** @var  mixed[]  */
		protected $options = [];

		/**
		 * @param $alias
		 * @param RendererInterface $renderer
		 * @param ProcessInterface $process
		 * @param ViewInterface $view
		 * @return string|null
		 */
		public function preRender($alias, RendererInterface $renderer, ProcessInterface $process, ViewInterface $view){}

		/**
		 * @param $alias
		 * @param RendererInterface $renderer
		 * @param \Jungle\Application\Dispatcher\ProcessInterface $process
		 * @param ViewInterface $view
		 * @return string|null
		 */
		public function postRender($alias, RendererInterface $renderer, ProcessInterface $process, ViewInterface $view){}

		/**
		 * @param $rendererAlias
		 * @param RendererInterface $renderer
		 * @param ResponseInterface $response
		 * @param ViewInterface $view
		 * @return mixed
		 */
		public function complete($rendererAlias, RendererInterface $renderer, ResponseInterface $response, ViewInterface $view){
			if(isset($this->rules[$rendererAlias]) && $this->rules[$rendererAlias] instanceof RendererRuleInterface){
				$this->rules[$rendererAlias]->complete($renderer, $response, $view);
			}
		}


		/**
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function setVar($key, $value){
			$this->variables[$key] = $value;
			return $this;
		}

		/**
		 * @param $key
		 * @return mixed|null
		 */
		public function getVar($key){
			return isset($this->variables[$key])?$this->variables[$key]:null;
		}

		/**
		 * @param array $variables
		 * @param bool|false $merge
		 * @return $this
		 */
		public function setVars(array $variables = [ ], $merge = false){
			$this->variables = $merge?array_replace($this->variables,$variables):$variables;
			return $this;
		}


		/**
		 * @param $name
		 * @param $value
		 * @return mixed
		 */
		public function setOption($name, $value){
			$this->options[$name] = $value;
			return $this;
		}

		/**
		 * @param $name
		 * @param null $default
		 * @return mixed
		 */
		public function getOption($name, $default = null){
			return isset($this->options[$name])?$this->options[$name]:$default;
		}

		/**
		 * @param array $options
		 * @param bool|false|false $merge
		 * @return $this
		 */
		public function setOptions(array $options, $merge = false){
			$this->options = $merge?array_replace($this->options,$options):$options;
			return $this;
		}

		/**
		 * @param $alias
		 * @param callable $rule
		 * @return $this
		 */
		public function addRule($alias, callable $rule){
			$this->rules[$alias] = $rule;
			return $this;
		}

		/**
		 * @param $alias
		 * @return callable|RendererRule|null
		 */
		public function getRule($alias){
			return isset($this->rules[$alias])?$this->rules[$alias]:null;
		}

		/**
		 * @param $alias
		 * @param array $defaults
		 * @return \mixed[]
		 */
		public function getVariables($alias, array $defaults = []){
			if(isset($this->rules[$alias])){
				$rule = $this->rules[$alias];
				if($rule instanceof RendererRule){
					return array_replace($defaults, $this->variables, $rule->getVariables());
				}
			}
			return array_replace($defaults, $this->variables);
		}

		/**
		 * @param $alias
		 * @param array $defaults
		 * @return array
		 */
		public function getOptions($alias, array $defaults = [ ]){
			if(isset($this->rules[$alias])){
				$rule = $this->rules[$alias];
				if($rule instanceof RendererRule){
					return array_replace($defaults, $this->options, $rule->getOptions());
				}
			}
			return array_replace($defaults, $this->options);
		}

		/**
		 * @param RequestInterface $request
		 * @param \Jungle\Application\Dispatcher\ProcessInterface $process
		 * @param ViewInterface $view
		 * @return string|null
		 */
		public function match(RequestInterface $request, ProcessInterface $process, ViewInterface $view){
			foreach($this->rules as $alias => $rule){
				if(call_user_func($rule, $request, $process, $view)){
					return $alias;
				}
			}
			return null;
		}

		/**
		 * @param $alias
		 * @param ViewInterface $view
		 * @param RendererInterface $renderer
		 * @param \Jungle\Application\Dispatcher\ProcessInterface $process
		 * @param array $variables
		 * @param array $options
		 * @return mixed
		 */
		public function render($alias,
			ViewInterface $view,
			RendererInterface $renderer,
			ProcessInterface $process,
			array $variables = [ ],
			array $options = [ ]
		){
			$variables = array_replace(
				$view->getVariables(),
				$this->variables,
				$renderer->getVariables(),
				$this->getVariables($alias),
				$variables
			);
			$options = array_replace(
				$view->getOptions(),
				$this->options,
				$renderer->getOptions(),
				$this->getVariables($alias),
				$options
			);
			return $renderer->render($process, $view, $variables, $options);
		}
	}
}

