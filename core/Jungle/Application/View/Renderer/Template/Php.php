<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.07.2016
 * Time: 21:22
 */
namespace Jungle\Application\View\Renderer\Template {
	
	use Jungle\Application\View\Renderer\Template;

	/**
	 * Class Php
	 * @package Jungle\Application\View\Renderer\Template
	 */
	class Php extends Template{

		/** @var string  */
		protected $type = 'php';

		/**
		 * @param $name
		 * @return boolean
		 */
		public function templateExists($name){
			// TODO: Implement templateExists() method.
		}

		/**
		 * @param $name
		 * @param array $variables
		 * @param bool|false $isBreakLevel
		 * @return string
		 */
		protected function _renderTemplate($name, array $variables = [ ], & $isBreakLevel = false){
			// TODO: Implement _renderTemplate() method.
		}

		/**
		 * @return mixed
		 */
		protected function _initEngine(){
			// TODO: Implement _initEngine() method.
		}

		/**
		 * @param $engine
		 */
		protected function _initExtensions($engine){
			// TODO: Implement _initExtensions() method.
		}

		/**
		 * @param $engine
		 */
		protected function _initTemplateLoader($engine){
			// TODO: Implement _initTemplateLoader() method.
		}
	}
}

