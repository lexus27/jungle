<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 20:34
 */
namespace Jungle\Util\Communication {

	use Jungle\Util\Communication\Sequence\ProcessSequenceInterface;
	use Jungle\Util\Communication\Sequence\SpecificationInterface;

	/**
	 * Interface SequenceInterface
	 * @package Jungle\Util\Communication
	 */
	interface SequenceInterface extends ConnectionInteractionInterface{


		/**
		 * @param array $sequence
		 * @throws Exception
		 */
		public function setSequence(array $sequence);



		/**
		 * @param SpecificationInterface $specification
		 * @return $this
		 */
		public function setSpecification(SpecificationInterface $specification);



		/**
		 * @return SpecificationInterface
		 */
		public function getSpecification();


		/**
		 * @return ProcessSequenceInterface
		 */
		public function run();

	}
}

