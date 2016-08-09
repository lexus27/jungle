<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.07.2016
 * Time: 2:46
 */
namespace Jungle\Application\View\Renderer\TemplateEngine {
	
	use Jungle\Application\View\Renderer\TemplateEngine;
	use Jungle\Loader;

	/**
	 * Class Twig
	 * @package Jungle\Application\View\RendererInterface\TemplateEngine
	 */
	class Twig extends TemplateEngine{

		/** @var  \Twig_Environment */
		protected $twig;

		/** @var string */
		protected $type = 'template-twig';

		/** @var  string */
		protected $default_extension = 'twig';

		/**
		 * @return mixed
		 */
		protected function _initEngine(){
			$twig = new \Twig_Environment(null,[
				'cache' => $this->options['cacheable']?$this->getCacheDirname():false
			]);
			return $this->twig = $twig;
		}

		/**
		 * @param \Twig_Environment $engine
		 * @return mixed
		 */
		protected function _initScope($engine){
			foreach($this->variables as $variable => $value){
				$engine->addGlobal($variable, $value);
			}
		}


		/**
		 * @param \Twig_Environment $engine
		 */
		protected function _initTemplateLoader($engine){
			$loader = new \Twig_Loader_Filesystem([
				$this->getBaseDirname()
			]);
			$engine->setLoader($loader);
		}

		/**
		 * @param \Twig_Environment $engine
		 */
		protected function _initExtensions($engine){
			$function = new \Twig_SimpleFunction('content',function($context){
				return isset($context['content'])?$context['content']:null;
			},[
				'needs_context' => true,
				'is_safe' => ['html']
			]);
			$engine->addFunction($function);
			$function = new \Twig_SimpleFunction('stopBubble',function($context){
				$context['__level_break'] = true;
				return '';
			},[
				'needs_context' => true
			]);
			$engine->addFunction($function);
		}

		/**
		 * @param array $options
		 * @param array $variables
		 */
		protected function _beforeRender(array $options, array $variables){

		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function templateExists($name){
			$loader = $this->twig->getLoader();
			if($loader instanceof \Twig_ExistsLoaderInterface){
				return $loader->exists($name);
			}
			return false;
		}

		/**
		 * @param $name
		 * @param array $variables
		 * @param bool|false $break
		 * @return string
		 */
		protected function _renderTemplate($name, array $variables = [ ], & $break = false){
			$variables = array_replace($variables,[
				'di'        => $this->process->getDispatcher()->getDi(),
				'process'   => $this->process,
			    '__level_break' => & $break
			]);
			return $this->twig->render($name, $variables);
		}


	}
}

