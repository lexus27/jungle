<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.05.2016
 * Time: 2:07
 */
namespace Jungle\Application\Dispatcher\Module {
	
	use Jungle\Application\Dispatcher\Module;

	/**
	 * Class DynamicModule
	 * @package Jungle\Application\Dispatcher\Module
	 */
	class DynamicModule extends Module{

		/** @var  string */
		protected $cache_dirname;

		/**
		 * @param $dirname
		 * @return $this
		 */
		public function setCacheDirname($dirname){
			$this->cache_dirname = $dirname;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getCacheDirname(){
			return $this->cache_dirname;
		}

	}
}

