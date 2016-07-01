<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 08.01.2016
 * Time: 16:24
 */
namespace Jungle\Util\Specifications\TextTransfer {

	/**
	 * Interface BodyInterface
	 * @package Jungle\HeaderCover
	 */
	interface BodyInterface{

		/**
		 * @return mixed
		 */
		public function getRaw();

		/**
		 * @param $raw
		 * @return mixed
		 */
		public function setRaw($raw);

		/**
		 * @see getPreparedContent
		 * @return mixed
		 */
		public function __toString();


		/**
		 * @param Document $document
		 * @return $this
		 */
		public function setDocument(Document $document=null);

		/**
		 * @return Document
		 */
		public function getDocument();


	}
}

