<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.07.2016
 * Time: 21:16
 */
namespace Jungle\Application {
	
	use Jungle\Application\Dispatcher\Controller\ProcessInterface;

	/**
	 * Interface ViewInterface
	 * @package Jungle\Application\View
	 */
	interface ViewInterface{
//
//		/**
//		 * @param $name
//		 * @param $value
//		 * @return $this
//		 */
//		public function setVar($name, $value);
//
//		/**
//		 * @param array $variables
//		 * @param bool|false $merge
//		 * @return $this
//		 */
//		public function setVars(array $variables = [ ], $merge = false);
//
//		public function setOption($name, $value);
//
//		/**
//		 * @param $name
//		 * @param null $default
//		 * @return mixed
//		 */
//		public function getOption($name, $default = null);
//
//		/**
//		 * @param array $options
//		 * @param bool|false $merge
//		 * @return mixed
//		 */
//		public function setOptions(array $options, $merge = false);

		/**
		 * @param ProcessInterface $process
		 * @return string renderer name
		 */
		public function match(ProcessInterface $process);

		/**
		 * @param ProcessInterface $process
		 * @param null $renderer_name
		 * @return mixed
		 */
		public function render(ProcessInterface $process, $renderer_name = null);

	}
}

