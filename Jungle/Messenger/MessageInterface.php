<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.12.2015
 * Time: 22:26
 */
namespace Jungle\Messenger {

	/**
	 * Interface MessageInterface
	 * @package Jungle\Messenger
	 */
	interface MessageInterface{

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

