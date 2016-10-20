<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 20:13
 */
namespace Jungle\Util\Communication\ApiInteractingStream {

	use Jungle\Util\Communication\Stream\StreamInteractionInterface;
	use Jungle\Util\Replacer\Replacer;
	use Jungle\Util\Replacer\ReplacerInterface;

	/**
	 * Class Api
	 * @package Jungle\Util\Communication\ApiInteractingStream
	 */
	abstract class Api extends \Jungle\Util\Communication\ApiInteracting\Api{

		/** @var  int */
		protected $max_length = 512;

		/** @var  ReplacerInterface */
		protected $replacer;

		/**
		 * @return Replacer|ReplacerInterface
		 */
		public function getReplacer(){
			if(!$this->replacer){
				$this->replacer = new Replacer();
			}
			return $this->replacer;
		}

		/**
		 * @param $answer
		 */
		abstract public function code($answer);

		/**
		 * @param Process $process
		 */
		abstract public function validateProcess(Process $process);

		/**
		 * @param $command
		 */
		protected function packCommand($command){
			return $command;
		}

		/**
		 * @param \Jungle\Util\Communication\Stream\StreamInteractionInterface $stream $sequence
		 * @return mixed
		 */
		public function read(StreamInteractionInterface $stream){
			return $stream->read($this->max_length);
		}

		/**
		 * @param StreamInteractionInterface $stream
		 * @param $data
		 * @return mixed
		 */
		public function send(StreamInteractionInterface $stream, $data){
			return $stream->write($this->packCommand($data));
		}
	}
}

