<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 19:09
 */
namespace Jungle\Util\Communication\Sequence {

	use Jungle\Util\Communication\Sequence;

	/**
	 * Interface CommandInterface
	 * @package Jungle\Util\Communication\Sequence
	 */
	interface CommandInterface{

		/**
		 * @param SpecificationInterface $specification
		 * @return mixed
		 */
		public function setSpecification(SpecificationInterface $specification);

		/**
		 * @return mixed
		 */
		public function getSpecification();

		/**
		 * @param ProcessSequenceInterface $sequence
		 * @param array $params
		 * @return ProcessInterface
		 */
		public function run(ProcessSequenceInterface $sequence, array $params);

	}
}

