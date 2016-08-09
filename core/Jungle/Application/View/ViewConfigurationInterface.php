<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.07.2016
 * Time: 20:56
 */
namespace Jungle\Application\View {

	/**
	 * Interface ViewConfigurationInterface
	 * @package Jungle\Application\View
	 */
	interface ViewConfigurationInterface{

		/**
		 * @param $name
		 * @param $value
		 * @return $this
		 */
		public function setVar($name, $value);

		/**
		 * @param $key
		 * @return mixed|null
		 */
		public function getVar($key);

		/**
		 * @param array $variables
		 * @param bool|false $merge
		 * @return $this
		 */
		public function setVars(array $variables = [ ], $merge = false);

		/**
		 * @param $name
		 * @param $value
		 * @return $this
		 */
		public function setOption($name, $value);

		/**
		 * @param $name
		 * @param null $default
		 * @return mixed
		 */
		public function getOption($name, $default = null);

		/**
		 * @param array $options
		 * @param bool|false $merge
		 * @return $this
		 */
		public function setOptions(array $options, $merge = false);

	}
}

