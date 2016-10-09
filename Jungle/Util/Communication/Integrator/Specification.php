<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 15:47
 */
namespace Jungle\Util\Communication\Integrator {

	/**
	 * Class Specification
	 * @package Jungle\Util\Communication\Integrator
	 */
	class Specification implements SpecificationInterface{

		/** @var array  */
		protected $methods = [];


		/**
		 * @param $name
		 * @param MethodInterface $method
		 * @return $this
		 */
		public function setMethod($name, MethodInterface $method){
			$this->methods[$name] = $method;
			$method->setSpecification($this);
			return $this;
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function getMethod($name){
			return isset($this->methods[$name])?$this->methods[$name]:null;
		}
	}
}

