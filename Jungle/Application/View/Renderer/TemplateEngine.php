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

	use Jungle\Application\Dispatcher\ProcessInterface;
	use Jungle\Application\View\Renderer;
	use Jungle\Application\ViewInterface;
	use Jungle\Loader;

	/**
	 * Class TemplateEngine
	 * @package Jungle\Application
	 */
	abstract class TemplateEngine extends Renderer{

		const RENDER_LEVEL_ACTION       = 0;
		const RENDER_LEVEL_CONTROLLER   = 1;
		const RENDER_LEVEL_MODULE       = 2;

		/** @var string  */
		protected $type = 'template';

		/** @var string */
		protected $mime_type = 'text/html';

		/** @var  array */
		protected $variables = [];

		/** @var  string */
		protected $default_extension = 'html';

		/** @var  ProcessInterface */
		protected $process;

		/** @var  string*/
		protected $base_dirname;

		/** @var  string */
		protected $cache_dirname;


		/**
		 * TemplateEngine constructor.
		 * @param null $mime_type
		 * @param string $ext
		 * @param array $options
		 * @param array $variables
		 */
		public function __construct($mime_type = null, $ext = null, array $options = [], array $variables = []){
			if($ext!==null){
				$this->options['extension'] = $ext;
			}
			parent::__construct($mime_type, $options, $variables);
		}

		/**
		 * Initializer.
		 */
		protected function _doInitialize(){
			foreach($this->getDefaultOptions() as $k => $v){
				if(!array_key_exists($k,$this->options)){
					$this->options[$k] = $v;
				}
			}
			$engine = $this->_initEngine();
			$this->_initScope($engine);
			$this->_initTemplateLoader($engine);
			$this->_initExtensions($engine);
		}

		/**
		 * @param array $defined
		 * @return array
		 */
		public function getDefaultOptions(array $defined = [ ]){
			$defined['render_level']    = self::RENDER_LEVEL_MODULE;
			$defined['cacheable']       = true;
			$defined['extension']       = $this->default_extension;
			return $defined;
		}

		/**
		 * @param $engine
		 * @return mixed
		 */
		abstract protected function _initScope($engine);

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
		public function setExtension($extension){
			$this->options['extension'] = $extension;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getExtension(){
			return $this->options['extension'];
		}

		/**
		 * @return string
		 */
		public function getBaseDirname(){
			if($this->base_dirname){
				return $this->base_dirname;
			}
			return $this->view->getBaseDirname();
		}

		/**
		 * @return string
		 */
		public function getCacheDirname(){
			if($this->base_dirname){
				return $this->base_dirname;
			}
			return $this->view->getCacheDirname() . DIRECTORY_SEPARATOR . $this->getName();
		}

		/**
		 * @return int
		 */
		public function getRenderLevel(){
			return $this->options['render_level'];
		}

		/**
		 * @param $level
		 * @return $this
		 */
		public function setRenderLevel($level){
			$this->options['render_level'] = $level;
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
		 * @param $template_name
		 * @param array $context
		 * @return string
		 */
		public function templateRender($template_name, array $context){
			if($this->templateExists($template_name)){
				return $this->_renderTemplate($template_name, $context);
			}
			return '';
		}

		/**
		 * @param array $options
		 * @return array
		 */
		protected function _optionsBeforeRender(array $options = [ ]){
			return array_replace($this->options, $options);
		}

		/**
		 * @param array $options
		 * @param array $variables
		 */
		protected function _beforeRender(array $options, array $variables){}

		/**
		 * @param ProcessInterface $process
		 * @param ViewInterface $view
		 * @param array $variables
		 * @param array $options
		 * @return string
		 */
		public function render(ProcessInterface $process, ViewInterface $view, array $variables = [], array $options = []){
			$this->process = $process; $r = $process->getReference();
			$levels = $this->_prepareTemplateLevels([ $r['action'], $r['controller'], $r['module'] ]);
			$options = $this->_optionsBeforeRender($options);
			$variables = $this->prepareVariables($process, $variables);
			$this->_beforeRender($options,$variables);
			$content = null;
			$isBreakLevel = false;
			foreach($levels as $level => $templateName){
				if($options['render_level'] < $level){
					return $content;
				}
				if($this->templateExists($templateName)){
					$content = $this->_renderTemplate(
						$templateName,
						array_replace($variables,[ 'content' => $content ]),
						$isBreakLevel
					);
					if($isBreakLevel) return $content;
				}elseif(!$level){
					$ob = $process->getOutputBuffer();
					if($ob) $content = $ob;
				}
			}
			return $content;
		}

		/**
		 * @param ProcessInterface $process
		 * @param array $variables
		 * @return array
		 */
		protected function prepareVariables(ProcessInterface $process, array $variables){
			$result = $process->getResult();
			if($result && !is_array($result)){
				$default_param = $process->getRouting()->getRoute()->getDefaultParam();
				if($default_param){
					$result = [
						$default_param => $result
					];
				}else{
					$result = [
						'result' => $result
					];
				}
			}
			if(is_array($result)){
				$variables = array_replace($variables, $result);
			}

			return $variables;
		}

		/**
		 * @param $name
		 * @return boolean
		 */
		abstract public function templateExists($name);

		/**
		 * @param $name
		 * @param array $variables
		 * @param bool|false $break
		 * @return string
		 */
		abstract protected function _renderTemplate($name, array $variables = [], & $break = false);

		/**
		 * @param $levelNames
		 * @return array
		 */
		protected function _prepareTemplateLevels(array $levelNames){
			$levels = [];$ext = $this->getExtension();
			$name = $this->getName();
			$levelNames = array_reverse($levelNames);
			for( $i = 0, $c = count($levelNames) ; $i < $c ; $i++ ){
				$levels[$i] = $name . '/' . $this->_prepareTemplateName(implode('/',$levelNames),$ext);
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
				$extension = $this->getExtension();
			}
			return $templateName . '.' . $extension;
		}

	}
}

