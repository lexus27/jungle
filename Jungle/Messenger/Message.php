<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.12.2015
 * Time: 22:30
 */
namespace Jungle\Messenger {

	/**
	 * Class Message
	 * @package Jungle\Messenger
	 */
	class Message implements MessageInterface{

		protected $content;

		/**
		 * @return string
		 */
		public function getContent(){
			return $this->content;
		}

		/**
		 * @param string $content
		 * @return $this
		 */
		public function setContent($content){
			$this->content = $content;
		}
	}
}

