<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.07.2016
 * Time: 3:04
 */
namespace Jungle\Application {
	
	use Jungle\Application\Dispatcher\ProcessInterface;
	use Jungle\Application\View\RendererInterface;
	use Jungle\Application\View\ViewStrategy\Exception\NotFoundSuitable;
	use Jungle\Di\Injectable;

	/**
	 *
	 * Отвечает за Отображение контента, Имеет зависимости от Стратегии
	 *
	 * Class View
	 * @package Jungle\Application\View
	 */
	class View extends Injectable implements ViewInterface{
 
		/** @var  RendererInterface[] */
		protected $renderers = [];

		/** @var  RendererInterface */
		protected $last_renderer;

		/** @var  string */
		protected $last_renderer_alias;

		/** @var  mixed[]  */
		protected $variables = [];

		/** @var  mixed[]  */
		protected $options = [];

		/** @var  string */
		protected $base_dirname;

		/** @var  string */
		protected $cache_dirname;

		/**
		 * @return string
		 */
		public function getBaseDirname(){
			return $this->base_dirname;
		}

		/**
		 * @param string $base_dirname
		 * @return $this
		 */
		public function setBaseDirname($base_dirname){
			$this->base_dirname = $base_dirname;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getCacheDirname(){
			return $this->cache_dirname;
		}

		/**
		 * @param string $cache_dirname
		 * @return $this
		 */
		public function setCacheDirname($cache_dirname){
			$this->cache_dirname = $cache_dirname;
			return $this;
		}

		/**
		 * @param $alias
		 * @return $this
		 */
		public function setDefaultRendererAlias($alias){
			$this->setOption('default_renderer', $alias);
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDefaultRendererAlias(){
			return $this->getOption('default_renderer');
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
		 * @param $value
		 */
		public function __set($key, $value){
			$this->variables[$key] = $value;
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
		 * @return $this
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
		 * @return mixed[]
		 */
		public function getVariables(){
			return $this->variables;
		}

		/**
		 * @return mixed[]
		 */
		public function getOptions(){
			return $this->options;
		}



		/**
		 * @param $alias
		 * @param RendererInterface $renderer
		 * @return $this
		 */
		public function setRenderer($alias, View\RendererInterface $renderer){
			$this->renderers[$alias] = $renderer;
			$renderer->setView($this);
			return $this;
		}

		/**
		 * @param $alias
		 * @return RendererInterface|null
		 */
		public function getRenderer($alias){
			return isset($this->renderers[$alias])? $this->renderers[$alias]:null;
		}

		/**
		 * @return RendererInterface
		 */
		public function getLastRenderer(){
			return $this->last_renderer;
		}

		/**
		 * @return string
		 */
		public function getLastRendererAlias(){
			return $this->last_renderer_alias;
		}

		/**
		 * @return string[]
		 */
		public function getRendererAliases(){
			return array_keys($this->renderers);
		}

		/**
		 * @param RendererInterface $renderer
		 * @return string|false
		 */
		public function getRendererName(RendererInterface $renderer){
			return array_search($renderer, $this->renderers, true);
		}


		/**
		 * @param string|null $alias
		 * @param ProcessInterface $process
		 * @param array $variables
		 * @param array|null $options
		 * @return string
		 * @throws Exception
		 */
		public function render($alias, ProcessInterface $process, array $variables = [], array $options = []){
			$viewStrategy = $this->view_strategy;
			if(is_null($alias)){
				$alias = $viewStrategy->match($this->request, $process, $this);
				if(!$alias){
					throw new NotFoundSuitable('Not suitable view for request');
				}
			}
			if(!isset($this->renderers[$alias])){
				throw new Exception('Renderer with alias "'.$alias.'" not found');
			}

			$renderer = $this->renderers[$alias];
			$renderer->initialize();
			$this->last_renderer = $renderer;
			$this->last_renderer_alias = $alias;
			$rendered = $viewStrategy->preRender($alias, $renderer, $process, $this);
			$rendered.= $viewStrategy->render($alias, $this, $renderer, $process,
				array_replace($this->variables,$variables),
				array_replace($this->options,$options)
			);
			$rendered.= $viewStrategy->postRender($alias, $renderer, $process, $this);
			return $rendered;
		}



	}
}

