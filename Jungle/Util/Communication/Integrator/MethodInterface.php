<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 16:04
 */
namespace Jungle\Util\Communication\Integrator {

	/**
	 * Interface MethodInterface
	 * @package Jungle\Util\Communication\Integrator
	 */
	interface MethodInterface{

		/**
		 * @param SpecificationInterface $specification
		 * @return $this
		 */
		public function setSpecification(SpecificationInterface $specification);

		/**
		 * @return SpecificationInterface
		 */
		public function getSpecification();

		/**
		 * @return void
		 */
		public function execute();

	}
}

