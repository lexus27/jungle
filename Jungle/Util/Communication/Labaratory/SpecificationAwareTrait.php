<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 17:23
 */
namespace Jungle\Util\Communication\Labaratory {

	/**
	 * Class SpecificationAwareTrait
	 * @package Jungle\Util\Communication\Labaratory
	 */
	trait SpecificationAwareTrait{

		/** @var  SpecificationInterface */
		protected $specification;


		/**
		 * @param SpecificationInterface $specification
		 * @return mixed
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


	}
}
