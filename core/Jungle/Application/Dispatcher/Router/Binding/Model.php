<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.07.2016
 * Time: 6:44
 */
namespace Jungle\Application\Dispatcher\Router\Binding {
	
	use Jungle\Application\Dispatcher\Router\BindingInterface;

	/**
	 * Class Model
	 * @package Jungle\Application\Dispatcher\Router\Binding
	 */
	class Model implements BindingInterface{

		protected $model_name;

		protected $depends = [];

		/**
		 * @param array $depends
		 */
		public function setDepends(array $depends){
			foreach($depends as $model_field => $route_placeholder){
				$this->depends[$model_field] = $route_placeholder;
			}
		}

		public function composite(array $params){
			// TODO: Implement composite() method.
		}

		public function decomposite($value){
			// TODO: Implement decomposite() method.
		}


		public function findObject(array $params){

		}
	}
}

