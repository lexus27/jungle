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

		use MessageTrait;

		/** @var string  */
		protected $type = 'Aggregation';

		/** @var  MessageInterface[]  */
		protected $messages = [];

		/** @var  array */
		protected $info = [];

		/**
		 * AggregationMessageTrait constructor.
		 * @param $messages
		 */
		public function __construct($messages){
			$this->messages = $messages;
		}

		/**
		 * @return MessageInterface[]
		 */
		public function getMessages(){
			return $this->messages;
		}

		/**
		 * @param array $info
		 * @return $this
		 */
		public function setInfo(array $info){
			$this->info = $info;
			return $this;
		}

		/**
		 * @return array
		 */
		public function getInfo(){
			return $this->info;
		}
	}
}
