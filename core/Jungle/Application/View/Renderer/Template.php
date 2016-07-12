<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.07.2016
 * Time: 0:06
 */
namespace Jungle\Application\View\Renderer {

	use Jungle\Application\Dispatcher\Controller\ProcessInterface;
	use Jungle\Application\View\Renderer;
	use Jungle\Loader;

	/**
	 * Class Template
	 * @package Jungle\Application
	 */
	abstract class Template extends Renderer{

		const RENDER_LEVEL_ACTION       = 0;
		const RENDER_LEVEL_CONTROLLER   = 1;
		const RENDER_LEVEL_MODULE       = 2;

		/** @var  callable */
		protected $autoloader;

		/** @var  int */
		protected $render_level = self::RENDER_LEVEL_MODULE;

		/** @var  array */
		protected $variables = [];

		/** @var  int */
		protected $template_file_extension;



		/** @var  array  */
		protected $default_level_templates = [];

		/** @var  ProcessInterface */
		protected $process;



		/** @var  string*/
		protected $base_dirname;

		/** @var  string */
		protected $cache_dirname;




		/**
		 * @param callable $autoloader
		 * @return $this
		 */
		public function setAutoloader(callable $autoloader){
			$this->autoloader = $autoloader;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getAutoloader(){
			return $this->autoloader;
		}

		protected function _doInitialize(){
			$this->_initAutoLoader();
			$engine = $this->_initEngine();
			$this->_initTemplateLoader($engine);
			$this->_initExtensions($engine);
		}

		/**
		 *
		 */
		protected function _initAutoLoader(){
			$autoloader = $this->getAutoloader();
			if($autoloader){
				call_user_func($autoloader,Loader::getDefault());
			}
		}

		/**
		 * @return mixed
		 */
		abstract protected function _initEngine();

		/**
		 * @param $engine
		 */
		abstract protected function _initExtensions($engine);

		/**
		 * @param $engine
		 */
		abstract protected function _initTemplateLoader($engine);

		/**
		 * @param $extension
		 * @return $this
		 */
		public function setTplFileExtension($extension){
			$this->template_file_extension = $extension;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getTplFileExtension(){
			return $this->template_file_extension;
		}


		/**
		 * @param $extension
		 * @return $this
		 */
		public function setTemplateExtension($extension){
			$this->template_file_extension = $extension;
			return $this;
		}

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
		 * @param $level
		 * @param $templateName
		 * @return $this
		 */
		public function setLevelTemplate($level, $templateName){
			$this->default_level_templates[$level] = $templateName;
			return $this;
		}

		/**
		 * @param $level
		 * @param null $default
		 * @return null
		 */
		public function getLevelTemplate($level, $default = null){
			return isset($this->default_level_templates[$level])? $this->default_level_templates[$level]:$default;
		}

		/**
		 * @return int
		 */
		public function getRenderLevel(){
			return $this->render_level;
		}

		/**
		 * @param $level
		 * @return $this
		 */
		public function setRenderLevel($level){
			$this->render_level = $level;
			return $this;
		}

		/**
		 * @param $name
		 * @param $value
		 * @return $this
		 */
		public function setVar($name, $value){
			$this->variables[$name] = $value;
			return $this;
		}

		/**
		 * @param ProcessInterface $process
		 * @param array $context_variables
		 * @return null|string
		 */
		public function render(ProcessInterface $process, array $context_variables = []){
			$this->process = $process;
			$reference = $process->getReference();
			$levels = $this->_prepareTemplateLevels([
				$reference['action'],
				$reference['controller'],
				$reference['module']
			]);
			$content = null;
			$isBreakLevel = false;
/*
			$result = $process->getResult();
			if(!$result){
				$buffer = $process->getOutputBuffer();
			}
*/
			foreach($levels as $level => $templateName){
				if(isset($buffer) && $level === 0){
					$content = $buffer;
					continue;
				}
				if($this->render_level < $level) return $content;
				$templateName = $this->getLevelTemplate($level, $templateName);
				if($this->templateExists($templateName)){
					$content = $this->_renderTemplate($templateName,[
						'content' => $content
					],$isBreakLevel);
					if($isBreakLevel) return $content;
				}
			}
			return $content;
			/**
			$moduleName = $reference['module'];
			$controllerName = $reference['controller'];
			$actionName = $reference['action'];

			$moduleTemplateName     = $moduleName . '.' . $this->template_file_extension;
			$controllerTemplateName = $moduleName . '/' . $controllerName . '.' . $this->template_file_extension;
			$actionTemplateName     = $moduleName . '/' . $controllerName . '/' . $actionName . '.' . $this->template_file_extension;

			$content = null;
			$isContinueLevel = false;

			if(!$isContinueLevel && $this->templateExists($actionTemplateName)){
				$content = $this->_renderTemplate($actionTemplateName,[],$isContinueLevel);
			}

			if($this->render_level < self::RENDER_LEVEL_CONTROLLER){
				return $content;
			}

			if(!$isContinueLevel && $this->templateExists($controllerTemplateName)){
				$content = $this->_renderTemplate($controllerTemplateName,[
					'content' => $content
				],$isContinueLevel);
			}

			if($this->render_level < self::RENDER_LEVEL_MODULE){
				return $content;
			}

			if(!$isContinueLevel && $this->templateExists($moduleTemplateName)){
				$content = $this->_renderTemplate($moduleTemplateName,[
					'content' => $content
				],$isContinueLevel);
			}

			return $content;
			*/
		}

		/**
		 *
		 */
		public function reset(){
			$this->render_level = 0;
			$this->default_level_templates = [];
		}

		/**
		 * @param $name
		 * @return boolean
		 */
		abstract public function templateExists($name);

		/**
		 * @param $name
		 * @param array $variables
		 * @param bool|false $isBreakLevel
		 * @return string
		 */
		abstract protected function _renderTemplate($name, array $variables = [], & $isBreakLevel = false);

		/**
		 * @param $levelNames
		 * @return array
		 */
		protected function _prepareTemplateLevels(array $levelNames){
			$levels = [];$ext = $this->getTplFileExtension();
			$levelNames = array_reverse($levelNames);
			for( $i = 0, $c = count($levelNames) ; $i < $c ; $i++ ){
				$levels[$i] = $this->_prepareTemplateName(implode('/',$levelNames),$ext);
				array_pop($levelNames);
			}
			return $levels;
		}

		/**
		 * @param $templateName
		 * @param null $extension
		 * @return string
		 */
		protected function _prepareTemplateName($templateName, $extension = null){
			if(is_null($extension)){
				$extension = $this->getTplFileExtension();
			}
			return $templateName . '.' . $extension;
		}

	}
}

