<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.10.2016
 * Time: 13:34
 */
namespace Jungle\Util\Buffer {

	use Jungle\Util\Buffer\BufferInterface;

	/**
	 * Interface BufferAwareInterface
	 * @package Jungle\Util\Communication\Hypertext
	 */
	interface BufferAwareInterface{

		/**
		 * @param BufferInterface|null $buffer
		 * @return $this
		 */
		public function setBuffer(BufferInterface $buffer = null);

		/**
		 * @return BufferInterface
		 */
		public function getBuffer();

	}
}

