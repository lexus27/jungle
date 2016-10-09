<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.10.2016
 * Time: 19:11
 */
namespace Jungle\Util\Specifications\Hypertext\Document {

	use Jungle\Util\BufferInterface;
	use Jungle\Util\Communication\Connection\StreamInteractionInterface;
	use Jungle\Util\Specifications\Hypertext\DocumentInterface;

	/**
	 * Interface ProcessorInterface
	 * @package Jungle\Util\Specifications\Hypertext\Document
	 */
	interface ProcessorInterface{

		/**
		 * @param DocumentInterface $document
		 * @return $this
		 */
		public function setDocument(DocumentInterface $document);

		/**
		 * @return DocumentInterface
		 */
		public function getDocument();


		/**
		 * @param bool|true $auto_close
		 * @return $this
		 */
		public function setSourceAutoClose($auto_close = true);

		/**
		 * @return boolean
		 */
		public function isSourceAutoClose();



		/**
		 * @param bool|true $auto_close
		 * @return $this
		 */
		public function setSourceAutoConnect($auto_close = true);

		/**
		 * @return boolean
		 */
		public function isSourceAutoConnect();

		/**
		 * @return bool
		 */
		public function isSourceStreamInteraction();


		/**
		 * @param array $config
		 * @param bool|false $merge
		 * @return mixed
		 */
		public function setConfig(array $config, $merge = false);


		/**
		 * @return bool
		 */
		public function isCompleted();

		/**
		 * @param $source
		 * @return mixed
		 */
		public function process($source);

		/**
		 * @return StreamInteractionInterface|string|null
		 */
		public function getSource();

		/**
		 *
		 */
		public function setBufferToString();

		/**
		 * @param BufferInterface $buffer
		 */
		public function setBuffer(BufferInterface $buffer = null);

		/**
		 * @return string|\Jungle\Util\BufferInterface|null
		 */
		public function getBuffer();

		/**
		 * @return string|null
		 */
		public function getBuffered();

	}
}

