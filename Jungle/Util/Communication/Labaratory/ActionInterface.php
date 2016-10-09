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
	 * Interface ActionInterface
	 * @package Jungle\Util\Communication\Labaratory
	 */
	interface ActionInterface extends SpecificationAwareInterface{

		/**
		 * @param ProcessStackInterface $stack
		 * @param array $params
		 * @return void
		 */
		public function execute(array $params, ProcessStackInterface $stack);

	}
}

