<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 08.01.2016
 * Time: 16:24
 */
namespace Jungle\Util\Communication\Hypertext {

	use Jungle\Util\Communication\Hypertext\Document\WriteProcessor;

	/**
	 * Interface ContentInterface
	 * @package Jungle\HeaderCover
	 */
	interface ContentInterface{

		/**
		 * @return string
		 */
		public function getContentType();

		/**
		 * @return string
		 */
		public function getContentLength();

		/**
		 * @param HeaderRegistryInterface $headerRegistry
		 * @param WriteProcessor $writer
		 * @return mixed
		 */
		public function beforeHeadersRender(HeaderRegistryInterface $headerRegistry, WriteProcessor $writer);


		/**
		 * @param $content
		 * @param HeaderRegistryInterface $headers
		 * @return void
		 */
		public function parse($content, HeaderRegistryInterface $headers);

		/**
		 * @return string
		 */
		public function __toString();

	}
}

