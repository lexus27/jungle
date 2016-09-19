<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.09.2016
 * Time: 12:32
 */
namespace Jungle\Util\Data\Foundation\Validation\Message {

	/**
	 * Class AggregationMessageTrait
	 * @package Jungle\Util\Data\Foundation\Validation\Message
	 */
	trait AggregationMessageTrait{

		/** @var  MessageInterface[]  */
		protected $messages = [];

		/**
		 * AggregationMessageTrait constructor.
		 * @param $type
		 * @param $messages
		 */
		public function __construct($type, $messages){
			$this->type = $type;
			$this->messages = $messages;
		}

		/**
		 * @return MessageInterface[]
		 */
		public function getMessages(){
			return $this->messages;
		}
	}
}
