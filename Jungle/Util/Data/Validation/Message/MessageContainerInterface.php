<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 15:22
 */
namespace Jungle\Util\Data\Validation\Message {

	use Jungle\Util\Data\Validation\MessageInterface;

	/**
	 * Interface MessageContainerInterface
	 * @package Jungle\Util\Data\Validation\Message
	 */
	interface MessageContainerInterface{

		/**
		 * @return bool
		 */
		public function isContainer();

		/**
		 * @param array $messages
		 * @return $this
		 */
		public function appendMessages(array $messages);

		/**
		 * @return MessageInterface[] | MessageContainerInterface[]
		 */
		public function getMessages();

	}
}

