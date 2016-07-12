<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.07.2016
 * Time: 2:46
 */
namespace Jungle\Application\View\Renderer\Template {
	
	use Jungle\Application\View\Renderer\Template;
	use Jungle\Loader;

	/**
	 * Class Twig
	 * @package Jungle\Application\View\RendererInterface\Template
	 */
	class Twig extends Template{

		/** @var bool  */
		protected static $twig_loaded = false;

		/** @var  \Twig_Environment */
		protected $twig;

		/** @var string  */
		protected $type = 'twig';

		/**
		 * Twig constructor.
		 * @param $dirname
		 * @param $cache_dirname
		 * @param $extension
		 */
		public function __construct($dirname, $cache_dirname, $extension){
			$this->setBaseDirname($dirname);
			$this->setCacheDirname($cache_dirname);
			$this->setTplFileExtension($extension);
		}


		/**
		 *
		 */
		protected function _initAutoLoader(){
			if(!self::$twig_loaded){
				self::$twig_loaded = true;
				parent::_initAutoLoader();
			}
		}

		/**
		 * @return mixed
		 */
		protected function _initEngine(){
			return $this->twig = new \Twig_Environment(null,[
				'cache' => $this->getCacheDirname()
			]);
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
			$function = new \Twig_SimpleFunction('getContent',function($context){
				return isset($context['content'])?$context['content']:null;
			},[
				'needs_context' => true, 'is_safe' => ['html']
			]);
			$engine->addFunction($function);
			$function = new \Twig_SimpleFunction('break',function($context){
				$context['__level_break'] = true;
				return '';
			},[
				'needs_context' => true
			]);
			$engine->addFunction($function);
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
		 * @param bool|false $isBreakLevel
		 * @return string
		 */
		protected function _renderTemplate($name, array $variables = [ ], & $isBreakLevel = false){
			$variables = array_replace($variables,[
				'process' => $this->process,
			    '__level_break' => & $isBreakLevel
			]);
			return $this->twig->render($name, $variables);
		}

	}
}

