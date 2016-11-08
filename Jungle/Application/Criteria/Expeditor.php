<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.11.2016
 * Time: 16:18
 */
namespace Jungle\Application\Criteria {

	use Jungle\Data\Record\Collection;

	/**
	 * Class Expeditor
	 * @package Jungle\Application\Criteria
	 */
	class Expeditor{


		/**
		 * @param $scope_key
		 * @param Collection $collection
		 * @param TransceiverInterface $transceiver
		 * @return Distributor
		 */
		public function distribute($scope_key, Collection $collection, TransceiverInterface $transceiver){
			return new Distributor($scope_key, $collection, $transceiver);
		}


	}
}

