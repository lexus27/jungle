<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 16.07.2016
 * Time: 3:03
 */
namespace Jungle\Application\View\Renderer\TemplateEngine {

	use Jungle\Application\View\Renderer\TemplateEngine;

	/**
	 * Class Smarty
	 * @package Jungle\Application\View\Renderer\TemplateEngine
	 */
	class Smarty extends TemplateEngine{

		protected $ext = 'tpl';

		/**
		 * @param $engine
		 * @return mixed
		 */
		protected function _initScope($engine){
			// TODO: Implement _initScope() method.
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
		 * @param bool|false $break
		 * @return string
		 */
		protected function _renderTemplate($name, array $variables = [ ], & $break = false){
			// TODO: Implement _renderTemplate() method.
		}

	}
}

