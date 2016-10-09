<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 18:58
 */
namespace Jungle\Util\Communication\Sequence {

	use Jungle\Util\Communication\Connection\StreamInteractionInterface;
	use Jungle\Util\Communication\ConnectionInterface;
	use Jungle\Util\Communication\Sequence;

	/**
	 * Interface SpecificationInterface
	 * @package Jungle\Util\Communication\Sequence
	 */
	interface SpecificationInterface{

		/**
		 * @return mixed
		 */
		public function getMaxLength();

		/**
		 * @param $response
		 * @return int
		 */
		public function recognizeCode($response);

		/**
		 * @param $command
		 * @return string
		 */
		public function convertBeforeSend($command);

		/**
		 * @param StreamInteractionInterface $connection
		 * @return mixed
		 */
		public function read(StreamInteractionInterface $connection);

		/**
		 * @param StreamInteractionInterface $connection
		 * @param $data
		 * @return mixed
		 */
		public function send(StreamInteractionInterface $connection, $data);

		/**
		 * @param $code
		 * @return mixed
		 */
		public function isFatalCode($code);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getCommand($key);

		/**
		 * @param $key
		 * @param CommandInterface $command
		 * @return mixed
		 */
		public function setCommand($key, CommandInterface $command);

		/**
		 * @return ConnectionInterface
		 */
		public function createConnection();

		/**
		 * @param ProcessSequenceInterface $processSequence
		 * @return mixed
		 */
		public function beforeSequence(ProcessSequenceInterface $processSequence);

		/**
		 * @param $processSequence
		 * @return mixed
		 */
		public function afterSequence(ProcessSequenceInterface $processSequence);

		/**
		 * @param ProcessSequenceInterface $processSequence
		 * @return mixed
		 */
		public function continueSequence(ProcessSequenceInterface $processSequence);

	}
}

