<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.11.2016
 * Time: 14:04
 */
namespace Jungle\Application\Criteria {

	/**
	 * Class Expeditor
	 * @package Jungle\Application\Criteria
	 */
	class Expeditor{

		/** @var  Distributor  */
		protected $distributors = [];


		public function setDistributor($key, Distributor $distributor){
			$this->distributors[$key] = $distributor;
		}







	}
}

