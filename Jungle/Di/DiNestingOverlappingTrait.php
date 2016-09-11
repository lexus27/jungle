<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 11.09.2016
 * Time: 11:38
 */
namespace Jungle\Di {

	/**
	 * Class DiNestingOverlappingTrait
	 * @package Jungle\Di
	 */
	trait DiNestingOverlappingTrait{

		/**
		 * @var bool
		 */
		protected $overlapping_mode = false;

		/**
		 * @var string
		 */
		protected $overlap_service_key;


		/**
		 * @param $existingServiceKey
		 * @param null $definition
		 * @return mixed
		 */
		public function setOverlapFrom($existingServiceKey, $definition = null){
			if(!$this->overlapping_mode){
				$this->overlapping_mode = true;
			}
			$this->overlap_service_key = $existingServiceKey;
			if($definition){
				$this->setShared($existingServiceKey,$definition);
			}
			return $this;
		}

		/**
		 * @param bool|false|false $overlap
		 * @return mixed
		 */
		public function useSelfOverlapping($overlap = false){
			$this->overlapping_mode = $overlap;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function isSelfOverlapping(){
			return $this->overlap_service_key;
		}

		/**
		 * @return null|string
		 */
		public function getOverlapKey(){
			return $this->overlap_service_key;
		}

	}
}
