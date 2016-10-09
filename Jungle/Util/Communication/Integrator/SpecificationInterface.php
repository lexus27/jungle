<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 16:05
 */
namespace Jungle\Util\Communication\Integrator {

	/**
	 * Interface SpecificationInterface
	 * @package Jungle\Util\Communication\Integrator
	 */
	interface SpecificationInterface{

		/**
		 * @param $name
		 * @param MethodInterface $method
		 * @return $this
		 */
		public function setMethod($name, MethodInterface $method);

		/**
		 * @param $name
		 * @return MethodInterface
		 */
		public function getMethod($name);

	}
}

