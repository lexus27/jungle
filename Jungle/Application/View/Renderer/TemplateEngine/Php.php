<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.07.2016
 * Time: 21:22
 */
namespace Jungle\Application\View\Renderer\TemplateEngine {
	
	use Jungle\Application\View\Exception;
	use Jungle\Application\View\Renderer\TemplateEngine;

	/**
	 * Class Php
	 * @package Jungle\Application\View\Renderer\TemplateEngine
	 */
	class Php extends TemplateEngine{

		/** @var string  */
		protected $type = 'template-php';

		/** @var  string */
		protected $default_extension = 'phtml';

		/** @var   */
		protected $template_base_dirname;

		protected $current_template_path;

		protected $template_variables = [];

		protected $error_reporting;

		protected $rendering = false;

		/**
		 * @param $name
		 * @return boolean
		 */
		public function templateExists($name){
			$full_template_name = $this->template_base_dirname . DIRECTORY_SEPARATOR . $name;
			return file_exists($full_template_name) && is_readable($full_template_name);
		}

		/**
		 * @param $name
		 * @param array $variables
		 * @param bool|false $break
		 * @return string
		 */
		protected function _renderTemplate($name, array $variables = [ ], & $break = false){
			$this->current_template_path = $this->template_base_dirname . DIRECTORY_SEPARATOR . $name;
			$this->_validatePath($this->current_template_path);
			if(file_exists($this->current_template_path)){
				$this->rendering = true;
				chdir(dirname($this->current_template_path));



				$_suppressErrors = $this->getOption('suppress_errors', true);
				if($_suppressErrors){
					$this->error_reporting = error_reporting();
					error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT | E_WARNING));
				}
				$process = $this->process;
				extract($variables);
				unset($variables);

				if(isset($content)){
					$this->template_variables['content'] = $content;
				}

				try{
					ob_start();
					include $this->current_template_path;
					$r = ob_get_clean();
					$break = $break || $this->break;
					return $r;
				}finally{
					$this->rendering = false;
					if($_suppressErrors){
						error_reporting($this->error_reporting);
					}
				}

			}
			return '';
		}

		/**
		 * @param array $options
		 * @param array $variables
		 */
		protected function _beforeRender(array $options, array $variables){
			parent::_beforeRender($options,$variables);
			$this->template_variables = $variables;
		}

		/**
		 * @param $path
		 * @throws Exception
		 */
		protected function _validatePath($path){
			if(strpos($path,"\0")!==false || strpos($path,"..")!==false){
				throw new Exception('Path is not valid');
			}
		}

		/**
		 * @param string $name
		 * @param array $arguments
		 */
		function __call($name, $arguments){
			// TODO: Implement __call() method.
		}

		/**
		 * @param string $name
		 * @return string
		 */
		function __get($name){
			return isset($this->template_variables[$name])?$this->template_variables[$name]:'';
		}

		/**
		 * @param string $name
		 * @param mixed $value
		 */
		function __set($name, $value){
			$this->template_variables[$name] = $value;
		}

		/**
		 * @param string $name
		 */
		function __unset($name){
			unset($this->template_variables[$name]);
		}


		/**
		 * @return mixed
		 */
		protected function _initEngine(){
			$this->template_base_dirname = $this->getBaseDirname();
		}

		/**
		 * @param $engine
		 * @return mixed
		 */
		protected function _initScope($engine){
			// TODO: Implement _initScope() method.
		}

		/**
		 * @param $engine
		 */
		protected function _initExtensions($engine){}

		/**
		 * @param $engine
		 */
		protected function _initTemplateLoader($engine){}


	}
}

