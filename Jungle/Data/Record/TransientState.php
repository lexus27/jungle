<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.05.2016
 * Time: 20:15
 */
namespace Jungle\Data\Record {

	/**
	 * Class TransientState
	 * @package Jungle\Data\Record
	 */
	class TransientState{

		/** @var  TransientState */
		protected $previous;

		/** @var  array */
		protected $data;

		/** @var bool */
		protected $fixed = false;

		/** @var  string|null */
		protected $tag;


		/**
		 * @param array $data
		 * @param null $tag
		 * @param TransientState|null $previous
		 * @return TransientState|null
		 */
		public static function checkout(array $data, $tag = null, TransientState $previous = null){
			if($previous){
				$data = array_diff_assoc($data, $previous->getForwardData());
			}
			if($data){
				return new TransientState($data, $tag, $previous);
			}else{
				return $previous;
			}
		}

		/**
		 * State constructor.
		 * @param array $data
		 * @param null $tag
		 * @param TransientState|null $previous
		 */
		protected function __construct(array $data, $tag = null, TransientState $previous = null){
			$this->data     = $data;
			$this->previous = $previous;
			$this->tag      = $tag;
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
		 * @return array
		 */
		public function getData(){
			return $this->data;
		}


		/**
		 * @param array $defaultFull Last fixed data
		 * @return array
		 */
		public function getForwardData(array $defaultFull = null){
			if($this->previous && !$this->previous->isFixed()){
				$data = $this->previous->isFixed()?[]:$this->previous->getForwardData();
			}else{
				return $this->data;
			}
			$data = array_replace($data, $this->data);
			if(is_array($defaultFull)){
				foreach($defaultFull as $property => $instantValue){
					if(!array_key_exists($property, $data)){
						$data[$property] = $instantValue;
					}
				}
			}
			return $data;
		}

		/**
		 * @return array
		 */
		public function getRollbackData(){
			$data = [];
			foreach($this->data as $key => $value){
				$data[$key] = $this->getPrevParam($key);
			}
			return $data;
		}


		/**
		 * @param $key
		 * @return mixed
		 */
		protected function getPrevParam($key){
			if($this->previous){
				if(array_key_exists($key, $this->previous->data)){
					return $this->previous->data[$key];
				}else{
					return $this->previous->getPrevParam($key);
				}
			}else{
				return null;
			}
		}

		/**
		 * @return bool
		 */
		public function clean(){
			if($this->previous){
				if($this->previous->fixed){
					$this->previous = null;
				}else{
					$this->previous->clean();
				}
				return true;
			}elseif($this->fixed){
				return false;
			}else{
				return true;
			}
		}

	}
}

