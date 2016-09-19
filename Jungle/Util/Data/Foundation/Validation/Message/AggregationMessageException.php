<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.09.2016
 * Time: 12:49
 */
namespace Jungle\Util\Data\Foundation\Validation\Message {
	
	use Jungle\Util\Data\Exception;

	/**
	 * Class AggregationMessageException
	 * @package Jungle\Util\Data\Foundation\Validation\Message
	 */
	class AggregationMessageException extends Exception implements AggregationMessageInterface{

		use AggregationMessageTrait;

		/**
		 * AggregationMessageException constructor.
		 * @param MessageInterface[] $messages
		 * @param array $info
		 * @param string $systemMessage
		 */
		public function __construct(array $messages, array $info = [], $systemMessage = ''){
			$this->messages = $messages;
			$this->info = $info;
			parent::__construct($systemMessage);
		}

	}
}

