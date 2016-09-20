<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 14:12
 */
namespace Jungle\Util\Data\Validation\Message {

	use Jungle\Util\Data\Validation\MessageInterface;

	/**
	 * Class ValidatorMessage
	 * @package Jungle\Util\Data\Validation\Message
	 */
	class ValidatorMessage extends ExpertizeMessage implements ValidatorMessageInterface,MessageContainerInterface{

		/** @var  string */
		protected $field_name;

		/** @var  bool */
		protected $container = false;

		/** @var  MessageInterface[]|null  */
		protected $messages;


		/**
		 * ValidatorMessage constructor.
		 * @param string $type
		 * @param string $field_name
		 * @param array $params
		 * @param array $messages
		 * @param string $systemMessage
		 */
		public function __construct($type, $field_name,array $params, array $messages = null, $systemMessage = ''){
			$this->field_name   = $field_name;
			if($messages!==null){
				$this->container = true;
				$this->messages = $messages;
			}
			parent::__construct($type, $params, $systemMessage);
		}

		/**
		 * @return string
		 */
		public function getFieldName(){
			return $this->field_name;
		}


		/**
		 * @return bool
		 */
		public function isContainer(){
			return $this->container;
		}

		/**
		 * @param array $messages
		 */
		public function setMessages(array $messages){
			$this->messages = $messages;
		}

		/**
		 * @param array $messages
		 * @return $this
		 */
		public function appendMessages(array $messages){
			if($this->container){
				$this->messages = array_merge($this->messages, $messages);
			}
			return $this;
		}


		/**
		 * @return MessageInterface[]|null
		 */
		public function getMessages(){
			return $this->messages;
		}

		/**
		 * @param null $field_name
		 * @return array
		 */
		public function decomposite($field_name = null){
			$leafs = [];
			if($this->container){
				foreach($this->messages as $message){
					if($message instanceof ValidatorMessage){
						if($field_name===null || $message->getFieldName() === $field_name){
							if($message->isContainer()){
								$leafs = array_merge($leafs, $message->decomposite($field_name));
							}else{
								$leafs[] = $message;
							}
						}
					}else{
						$leafs[] = $message;
					}
				}
			}
			return $leafs;
		}

	}
}

