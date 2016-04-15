<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.12.2015
 * Time: 22:26
 */
namespace Jungle\Messager {

	/**
	 * Interface IMessage
	 * @package Jungle\Messager
	 */
	interface IMessage{

		/**
		 * @return string
		 */
		public function getContent();

		/**
		 * @param string $content
		 * @return $this
		 */
		public function setContent($content);

	}
}

