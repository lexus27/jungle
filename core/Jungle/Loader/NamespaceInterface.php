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
	
	interface NamespaceInterface{

		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @return string
		 */
		public function getFullName();

		/**
		 * @return NamespaceInterface[]
		 */
		public function getChildren();

		/**
		 * @return ClassInterface[]
		 */
		public function getClasses();

	}
}

