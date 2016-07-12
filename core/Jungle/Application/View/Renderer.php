<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.07.2016
 * Time: 21:09
 */
namespace Jungle\Application\View {

	/**
	 * Class Renderer
	 * @package Jungle\Application\View
	 */
	abstract class Renderer implements RendererInterface{

		/** @var  bool */
		protected $initialized = false;

		/** @var  string */
		protected $type;

		/**
		 * @return string
		 */
		public function getType(){
			return $this->type;
		}

		/**
		 *
		 */
		public function initialize(){
			if(!$this->initialized){
				$this->initialized = true;
				$this->_doInitialize();
			}
		}

		abstract protected function _doInitialize();

	}
}

