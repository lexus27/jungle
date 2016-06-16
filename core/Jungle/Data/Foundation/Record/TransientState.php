<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.05.2016
 * Time: 20:15
 */
namespace Jungle\Data\Foundation\Record {

	/**
	 * Class TransientState
	 * @package Jungle\Data\Foundation\Record
	 */
	abstract class TransientState{

		/** @var  TransientState */
		protected $previous;

		/** @var  array */
		protected $data;

		/** @var bool */
		protected $fixed = false;


		/**
		 * State constructor.
		 * @param $data
		 * @param TransientState|null $previous
		 */
		public function __construct(array $data, TransientState $previous = null){
			$this->data = $data;
			$this->previous = $previous;
		}

		/**
		 * @return TransientState
		 */
		public function getPrevious(){
			return $this->previous;
		}

		/**
		 * @return bool
		 */
		public function isInitial(){
			return $this->fixed && !$this->previous;
		}

		/**
		 * @return bool
		 */
		public function isFixed(){
			return $this->fixed;
		}

		/**
		 * @param bool|true $fixed
		 * @return $this
		 */
		public function setFixed($fixed = true){
			$this->fixed = $fixed;
			return $this;
		}

		/**
		 * @param array $instant Last fixed data
		 * @return array
		 */
		public function getData(array $instant = null){
			if($this->previous && !$this->previous->isFixed()){
				$data = $this->previous->getData();
			}else{
				return $this->data;
			}
			$data = array_replace($data, $this->data);
			if(is_array($instant)){
				foreach($instant as $property => $instantValue){
					if(!array_key_exists($property, $data)){
						$data[$property] = $instantValue;
					}
				}
			}
			return $data;
		}

	}
}

