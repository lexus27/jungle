<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 17:35
 */
namespace Jungle\Util\Data\Foundation\Validation\Message {
	
	use Jungle\Util\Data\Foundation\Validation\Message;

	/**
	 * Class Aggregation
	 * @package Jungle\Util\Data\Foundation\Validation\Message
	 */
	class Aggregation extends Message implements AggregationInterface{

		/** @var  MessageInterface[]  */
		protected $messages = [];

		/**
		 * Aggregation constructor.
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

