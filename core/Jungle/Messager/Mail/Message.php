<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.12.2015
 * Time: 22:49
 */
namespace Jungle\Messager\Mail {

	/**
	 * Class Message
	 * @package Jungle\Messager\Mail
	 */
	class Message extends \Jungle\Messager\Message implements IMessage{

		/** @var string */
		protected $type;

		/** @var string */
		protected $subject;

		/** @var \Jungle\Messager\IContact|null */
		protected $author;

		/** @var IAttachment[] */
		protected $attachments = [];



		/**
		 * @param string $type
		 * @return $this
		 */
		public function setType($type){
			$this->type = $type;
		}

		/**
		 * @return string
		 */
		public function getType(){
			return $this->type;
		}


		/**
		 * @param Contact|string|array|null $contact
		 * @return $this
		 */
		public function setAuthor($contact = null){
			$this->author = $contact?Contact::getContact($contact):null;
			return $this;
		}

		/**
		 * @return \Jungle\Messager\IContact|null
		 */
		public function getAuthor(){
			return $this->author;
		}


		/**
		 * @param $subject
		 * @return $this
		 */
		public function setSubject($subject){
			$this->subject = $subject;
		}

		/**
		 * @return string
		 */
		public function getSubject(){
			return $this->subject;
		}

		/**
		 * @param IAttachment $attachment
		 * @return $this
		 */
		public function addAttachment(IAttachment $attachment){
			if($this->searchAttachment($attachment)===false){
				$this->attachments[] = $attachment;
			}
			return $this;
		}

		/**
		 * @param IAttachment $attachment
		 * @return bool|int
		 */
		public function searchAttachment(IAttachment $attachment){
			return array_search($attachment,$this->attachments,true);
		}

		/**
		 * @param IAttachment $attachment
		 * @return $this
		 */
		public function removeAttachment(IAttachment $attachment){
			if(($i = $this->searchAttachment($attachment))!==false){
				array_splice($this->attachments,$i,1);
			}
			return $this;
		}

		/**
		 * @return bool
		 */
		public function hasAttachments(){
			return (bool)$this->attachments;
		}

		/**
		 * @return IAttachment[]
		 */
		public function getAttachments(){
			return $this->attachments;
		}

	}
}

