<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 15:45
 */
namespace Jungle\Util\Data\Validation\Message {
	
	use Jungle\Util\Data\Validation\Message;

	/**
	 * Class ValidationCollector
	 * @package Jungle\Util\Data\Validation\Message
	 */
	class ValidationCollector extends ValidatorMessage{

		/** @var  string  */
		protected $type = 'Validation';

		/** @var  bool  */
		protected $container = true;

		/** @var  object */
		protected $object;

		/**
		 * ValidationCollector constructor.
		 * @param object $object
		 * @param array $messages
		 * @param string $systemMessage
		 */
		public function __construct($object = null, array $messages = [], $systemMessage = ''){
			$this->object = $object;
			parent::__construct(null, null, [], $messages, $systemMessage);
		}

		/**
		 * @param $systemMessage
		 * @return $this
		 */
		public function setSystemMessage($systemMessage){
			$this->message = $systemMessage;
			return $this;
		}

		/**
		 * @param $object
		 * @return $this
		 */
		public function setObject($object){
			$this->object = $object;
			return $this;
		}

		/**
		 * @return object
		 */
		public function getObject(){
			return $this->object;
		}

	}
}

