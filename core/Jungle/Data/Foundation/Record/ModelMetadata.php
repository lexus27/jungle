<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 14.06.2016
 * Time: 2:46
 */
namespace Jungle\Data\Foundation\Record {

	use Jungle\Data\Foundation\Record\ModelMetadata\Strategy;

	/**
	 * Class ModelMetadata
	 * @package Jungle\Data\Foundation\Record
	 */
	abstract class ModelMetadata{

		/** @var  Strategy */
		protected $strategy;

		/**
		 * @return Strategy
		 */
		public function getStrategy(){
			return $this->strategy;
		}

		/**
		 * @param Strategy $strategy
		 * @return $this
		 */
		public function setStrategy(Strategy $strategy){
			$this->strategy = $strategy;
			return $this;
		}



	}

}

