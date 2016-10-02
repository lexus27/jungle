<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 19:11
 */
namespace Jungle\Util\Communication\Sequence {

	use Jungle\Util\Communication\Sequence\Exception\RuleMessage;

	/**
	 * Interface RuleInterface
	 * @package Jungle\Util\Communication\Sequence
	 */
	interface RuleInterface{

		/**
		 * @param ProcessInterface $process
		 * @param ProcessSequenceInterface $processSequence
		 * @return RuleMessage|null
		 */
		public function check(ProcessInterface $process, ProcessSequenceInterface $processSequence);

		/**
		 * @param ProcessInterface $process
		 * @param ProcessSequenceInterface $processSequence
		 * @return void
		 */
		public function onNotMessages(ProcessInterface $process, ProcessSequenceInterface $processSequence);

	}
}

