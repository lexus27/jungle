<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.04.2016
 * Time: 21:20
 */
namespace routex {

	/**
	 * Class Router
	 * @package router
	 */
	class Router{

		/** @var array  */
		protected $_routes = [];

		/**
		 * @param Context $context
		 * @return bool
		 */
		public function match(Context $context){
			foreach($this->_routes as $route){
				$result = $route->match($context);
				if($result){
					return $result;
				}
			}
			return false;
		}


	}

	/**
	 * Class Route
	 * @package router
	 */
	class Route{

		protected $_pattern;

		protected $_pattern_compiled;

		/**
		 * @param Context $context
		 */
		public function match(Context $context){

		}

	}


	class Context{

		protected $_path;

		public function getPath(){
			return $this->_path;
		}

	}

}

