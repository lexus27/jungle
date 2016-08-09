<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.07.2016
 * Time: 20:51
 */
namespace Jungle\Application\View\ViewStrategy {
	
	use Jungle\Application\Dispatcher\ProcessInterface;
	use Jungle\Application\RequestInterface;
	use Jungle\Application\ResponseInterface;
	use Jungle\Application\View\RendererInterface;
	use Jungle\Application\View\ViewStrategyInterface;
	use Jungle\Application\ViewInterface;

	/**
	 * Class RendererRule
	 * @package Jungle\Application\View\ViewStrategy
	 */
	class RendererRule implements RendererRuleInterface{

		/** @var  callable */
		protected $matcher;

		/** @var  mixed[] */
		protected $variables = [];

		/** @var  mixed[] */
		protected $options = [];

		/**
		 * RendererRule constructor.
		 * @param callable $matcher(RequestInterface $request, ProcessInterface $process, ViewInterface $view)
		 */
		public function __construct(callable $matcher){
			$this->matcher = $matcher;
		}

		/**
		 * @param RequestInterface $request
		 * @param ProcessInterface $process
		 * @param ViewInterface $view
		 * @return bool
		 */
		public function __invoke(RequestInterface $request, ProcessInterface $process, ViewInterface $view){
			return ($this->matcher && !!call_user_func($this->matcher, $request, $process, $view));
		}

		/**
		 * @return \mixed[]
		 */
		public function getOptions(){
			return $this->options;
		}

		/**
		 * @return \mixed[]
		 */
		public function getVariables(){
			return $this->variables;
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
		 * @param RendererInterface $renderer
		 * @param ViewStrategyInterface $view_strategy
		 * @param ViewInterface $view
		 * @return null|string
		 */
		public function preRender(RendererInterface $renderer, ViewStrategyInterface $view_strategy, ViewInterface $view){
			return null;
		}

		/**
		 * @param RendererInterface $renderer
		 * @param ViewStrategyInterface $view_strategy
		 * @param ViewInterface $view
		 * @return null|string
		 */
		public function postRender(RendererInterface $renderer, ViewStrategyInterface $view_strategy, ViewInterface $view){
			return null;
		}

		/**
		 * @param RendererInterface $renderer
		 * @param ResponseInterface $response
		 * @param ViewInterface $view
		 */
		public function complete(RendererInterface $renderer, ResponseInterface $response, ViewInterface $view){

		}
	}
}

