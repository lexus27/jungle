<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.12.2015
 * Time: 22:30
 */
namespace Jungle\Messager {

	/**
	 * Class Message
	 * @package Jungle\Messager
	 */
	class Message implements IMessage{

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

