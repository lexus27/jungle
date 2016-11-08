<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 18:44
 */
namespace Jungle\Util\Communication\ApiInteracting {

	/**
	 * Interface ActionInterface
	 * @package Jungle\Util\Communication\ApiInteracting
	 */
	interface ActionInterface{

		/**
		 * @param array $params
		 * @param Combination $combination
		 * @return void
		 */
		public function execute(array $params, Combination $combination);

	}
}

