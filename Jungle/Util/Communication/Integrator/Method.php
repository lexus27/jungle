<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 16:10
 */
namespace Jungle\Util\Communication\Integrator {

	/**
	 * Class Method
	 * @package Jungle\Util\Communication\Integrator
	 */
	class Method implements MethodInterface{

		/** @var  SpecificationInterface */
		protected $specification;


		/** @var array  */
		protected $post = [];

		/** @var array  */
		protected $get = [];

		/** @var array  */
		protected $headers = [];

		/** @var array  */
		protected $cookies = [];

		/**
		 * @param SpecificationInterface $specification
		 * @return $this
		 */
		public function setSpecification(SpecificationInterface $specification){
			$this->specification = $specification;
			return $this;
		}

		/**
		 * @return SpecificationInterface
		 */
		public function getSpecification(){
			return $this->specification;
		}

		/**
		 *
		 */
		public function execute(){
			$process = new Process($this);




		}

	}
}

