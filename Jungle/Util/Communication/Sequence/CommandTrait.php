<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 23:02
 */
namespace Jungle\Util\Communication\Sequence {

	/**
	 * Class CommandTrait
	 * @package Jungle\Util\Communication\Sequence
	 */
	trait CommandTrait{

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
		 * @return mixed
		 */
		public function getSpecification(){
			return $this->specification;
		}

	}
}

