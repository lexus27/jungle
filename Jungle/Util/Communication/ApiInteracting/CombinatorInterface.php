<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 17:32
 */
namespace Jungle\Util\Communication\ApiInteracting {

	/**
	 * Interface CombinatorInterface
	 * @package Jungle\Util\Communication\ApiInteracting
	 */
	interface CombinatorInterface{

		/**
		 * @param Api $api
		 * @param Collector $collector
		 * @return Combination
		 */
		public function combine(Api $api, Collector $collector);

	}
}

