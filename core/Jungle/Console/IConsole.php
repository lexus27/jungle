<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 05.01.2016
 * Time: 0:06
 */
namespace Jungle\Console {

	/**
	 * Interface IConsole
	 * @package Jungle\Console
	 *
	 * Подвид интерфейса общения с субьектом
	 */
	interface IConsole{

		/**
		 * @param $command
		 * @return $this
		 */
		public function send($command);

		/**
		 * @return string
		 */
		public function read();

	}
}

