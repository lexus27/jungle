<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.07.2016
 * Time: 6:53
 */
namespace Jungle\Application\Dispatcher\Router {

	/**
	 * Class Binding
	 * @package Jungle\Application\Dispatcher\Router
	 */
	class Binding implements BindingInterface{

		/** @var  callable  */
		protected $decomposite_function;

		/** @var  callable  */
		protected $composite_function;

		protected $depends = [];

		/**
		 * Binding constructor.
		 * @param callable $decompositeFunction
		 * @param callable $compositeFunction
		 * @param array $depends
		 */
		public function __construct(callable $decompositeFunction,callable $compositeFunction,array $depends = []){
			$this->decomposite_function = $decompositeFunction;
			$this->composite_function = $compositeFunction;
			$this->depends = $depends;
		}


		/**
		 * @param array $params
		 * @return mixed
		 */
		public function composite(array $params){
			return call_user_func($this->composite_function,$params);
		}

		/**
		 * @param array $params
		 * @return array
		 */
		public function afterComposite(array $params){
			foreach($this->depends as $param){
				unset($params[$param]);
			}
			return $params;
		}

		/**
		 * @param $value
		 * @return mixed
		 */
		public function decomposite($value){
			return call_user_func($this->decomposite_function,$value);
		}

	}
}

