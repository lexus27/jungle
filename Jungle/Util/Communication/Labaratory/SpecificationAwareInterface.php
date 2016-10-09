<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 16:42
 */
namespace Jungle\Util\Communication\Labaratory {

	/**
	 * Interface SpecificationAwareInterface
	 * @package Jungle\Util\Communication\Labaratory
	 */
	interface SpecificationAwareInterface{

		/**
		 * @param SpecificationInterface $specification
		 * @return mixed
		 */
		public function setSpecification(SpecificationInterface $specification);

		/**
		 * @return SpecificationInterface
		 */
		public function getSpecification();

	}
}

