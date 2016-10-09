<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 16:30
 */
namespace Jungle\Util\Communication\Labaratory {

	/**
	 * Interface SpecificationInterface
	 * @package Jungle\Util\Communication\Labaratory
	 */
	interface SpecificationInterface{

		/**
		 * @param $key
		 * @param ActionInterface $action
		 * @return $this
		 */
		public function setAction($key, ActionInterface $action);

		/**
		 * @param $key
		 * @return ActionInterface
		 */
		public function getAction($key);




		/**
		 * @param SequenceProcessInterface $processSequence
		 * @return mixed
		 */
		public function beforeSequence(SequenceProcessInterface $processSequence);

		/**
		 * @param SequenceProcessInterface $processSequence
		 * @return mixed
		 */
		public function afterSequence(SequenceProcessInterface $processSequence);

		/**
		 * @param SequenceProcessInterface $processSequence
		 * @return mixed
		 */
		public function continueSequence(SequenceProcessInterface $processSequence);



	}
}

