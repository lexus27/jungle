<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.12.2015
 * Time: 22:37
 */
namespace Jungle\Messenger\Mail {

	/**
	 * Interface IMailMessage
	 * @package Jungle\Messenger\Mail
	 */
	interface IMessage extends \Jungle\Messenger\IMessage{

		/**
		 * @param Contact|string|array|null $contact
		 * @return $this
		 */
		public function setAuthor($contact=null);

		/**
		 * @return Contact|null
		 */
		public function getAuthor();



		/**
		 * @param string $type
		 * @return $this
		 */
		public function setType($type);

		/**
		 * @return string
		 */
		public function getType();


		/**
		 * @param $subject
		 * @return $this
		 */
		public function setSubject($subject);

		/**
		 * @return string
		 */
		public function getSubject();


		/**
		 * @param IAttachment $attachment
		 * @return $this
		 */
		public function addAttachment(IAttachment $attachment);

		/**
		 * @param IAttachment $attachment
		 * @return bool|int
		 */
		public function searchAttachment(IAttachment $attachment);

		/**
		 * @param IAttachment $attachment
		 * @return $this
		 */
		public function removeAttachment(IAttachment $attachment);

		/**
		 * @return bool
		 */
		public function hasAttachments();

		/**
		 * @return IAttachment[]
		 */
		public function getAttachments();

	}
}

