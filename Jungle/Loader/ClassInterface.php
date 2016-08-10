<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 11.07.2016
 * Time: 18:30
 */
namespace Jungle\Loader {

	/**
	 * Interface ClassInterface
	 * @package Jungle\Loader
	 */
	interface ClassInterface{

		public function getName();

		public function getFullName();

		public function getNamespaceName();



	}
}

