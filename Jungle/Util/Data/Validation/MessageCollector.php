<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 15:39
 */
namespace Jungle\Util\Data\Validation {

	/**
	 * Class MessageCollector
	 * @package Jungle\Util\Data\Validation
	 */
	class MessageCollector{

		/** @var array  */
		protected $messages = [];

		public function addMessage(MessageInterface $message){
			$this->messages[] = $message;
			return $this;
		}

		public function appendMessages(array $messages){
			$this->messages = array_merge($this->messages, $messages);
			return $this;
		}

		public function prependMessages(array $messages){
			$this->messages = array_merge($messages, $this->messages);
			return $this;
		}

	}
}

