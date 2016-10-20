<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 16:36
 */
namespace Jungle\Util\Communication\ApiInteracting {

	use Jungle\Util\ProcessUnit\ProcessCollector;

	/**
	 * Class Collector
	 * @package Jungle\Util\Communication\ApiInteracting
	 */
	class Collector extends ProcessCollector{

		/**
		 * Collector constructor.
		 * @param Api $api
		 */
		public function __construct(Api $api){
			$this->api = $api;
		}

		/**
		 * @return Api
		 */
		public function getApi(){
			return $this->api;
		}


	}
}

