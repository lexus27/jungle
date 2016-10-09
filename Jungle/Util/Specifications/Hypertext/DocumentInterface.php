<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.10.2016
 * Time: 0:35
 */
namespace Jungle\Util\Specifications\Hypertext {

	use Jungle\Util\Specifications\Hypertext\Document\ReadProcessor;
	use Jungle\Util\Specifications\Hypertext\Document\WriteProcessor;


	/**
	 * Interface DocumentInterface
	 * @package Jungle\Util\Specifications\Hypertext
	 */
	interface DocumentInterface extends HeaderRegistryInterface, ContentAwareInterface{

		/**
		 * @param string $contents
		 * @return string|null
		 */
		public function encodeContents($contents);

		/**
		 * @param string $contents
		 * @return string|null
		 */
		public function decodeContents($contents);

		/**
		 * @param WriteProcessor $writer
		 * @return void
		 */
		public function beforeWrite(WriteProcessor $writer);

		/**
		 * @param ReadProcessor $reader
		 * @return void
		 */
		public function beforeRead(ReadProcessor $reader);


		/**
		 * @param $data
		 * @param $readingIndex
		 * @return bool|void
		 */
		public function beforeHeaderRead($data, $readingIndex);

		/**
		 * @param ReadProcessor $reader
		 * @return void
		 */
		public function onHeadersRead(ReadProcessor $reader);

		/**
		 * @param ReadProcessor $reader
		 * @return void
		 */
		public function onContentsRead(ReadProcessor $reader);




		/**
		 * @return string
		 */
		public function __toString();



	}
}

