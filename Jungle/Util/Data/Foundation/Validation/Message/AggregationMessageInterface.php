<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 17:32
 */
namespace Jungle\Util\Data\Foundation\Validation\Message {

	/**
	 * Interface AggregationMessageInterface
	 * @package Jungle\Util\Data\Foundation\Validation\Message
	 */
	interface AggregationMessageInterface extends MessageInterface{

		/**
		 * @return MessageInterface[]
		 */
		public function getMessages();

		/**
		 * @return array
		 */
		public function getInfo();

		/**
		 * @param array $info
		 * @return $this
		 */
		public function setInfo(array $info);

	}
}

