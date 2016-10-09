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
	 * Interface SequenceInterface
	 * @package Jungle\Util\Communication\Labaratory
	 */
	interface SequenceInterface extends SpecificationAwareInterface{

		/**
		 * @param $action
		 * @param array $params
		 * @return ProcessInterface
		 */
		public function call($action, array $params);

		/**
		 * @param array $definition
		 * @return SequenceProcessInterface
		 */
		public function sequence(array $definition);

	}
}

