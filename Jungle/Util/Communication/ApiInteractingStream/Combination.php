<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 19:35
 */
namespace Jungle\Util\Communication\ApiInteractingStream {

	use Jungle\Util\Communication\Stream\StreamInteractionInterface;

	/**
	 * Class Combination
	 * @package Jungle\Util\Communication\ApiInteractingStream
	 */
	class Combination extends \Jungle\Util\Communication\ApiInteracting\Combination{

		/** @var  StreamInteractionInterface */
		protected $stream;

		/** @var array  */
		protected $default_params = [];

		/**
		 * Combination constructor.
		 * @param StreamInteractionInterface $stream
		 */
		public function __construct(StreamInteractionInterface $stream){
			$this->stream = $stream;
		}

		/**
		 * @param StreamInteractionInterface $stream
		 * @return $this
		 */
		public function setStream(StreamInteractionInterface $stream){
			$this->stream = $stream;
			return $this;
		}

		/**
		 * @return \Jungle\Util\Communication\Stream\StreamInteractionInterface
		 */
		public function getStream(){
			return $this->stream;
		}


	}
}

