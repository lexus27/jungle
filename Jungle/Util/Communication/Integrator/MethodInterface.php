<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 12:30
 */
namespace Jungle\Util\Communication\Integrator {

	/**
	 * Interface MethodInterface
	 * @package Jungle\Util\Communication\Integrator
	 */
	interface MethodInterface{

		/**
		 * @param $data
		 * @return mixed
		 */
		public function __invoke($data);

	}
}

