<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.04.2016
 * Time: 21:19
 */
namespace Jungle\Application {

	/**
	 * Interface RequestInterface
	 * @package Jungle\Application
	 */
	interface RequestInterface{

		/**
		 * @return string
		 */
		public function getPath();

		/**
		 * @return ResponseInterface
		 */
		public function getResponse();

		/**
		 * @param $key
		 * @param null $format
		 * @param null $default
		 * @return mixed
		 */
		public function getParam($key, $format = null, $default = null);

		/**
		 * @param $specificity
		 * @return bool
		 */
		public function hasSpecificity($specificity);

	}
}

