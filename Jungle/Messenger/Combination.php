<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.12.2015
 * Time: 22:30
 */
namespace Jungle\Messenger {

	/**
	 * Class Combination
	 * @package Jungle\Messenger\Mail
	 */
	class Combination implements ICombination{

		/**
		 * @var IContact[]
		 */
		protected $destinations = [];

		/**
		 * @var IMessage
		 */
		protected $message;

		/**
		 * @param IContact $destination
		 * @return $this
		 */
		public function addDestination(IContact $destination){
			if($this->searchDestination($destination)===false){
				$this->destinations[] = $destination;
			}
			return $this;
		}

		/**
		 * @param IContact $destination
		 * @return int|bool
		 */
		public function searchDestination(IContact $destination){
			return array_search($destination,$this->destinations,true);
		}

		/**
		 * @param IContact $destination
		 * @return $this
		 */
		public function removeDestination(IContact $destination){
			if(($i = $this->searchDestination($destination))!==false){
				array_splice($this->destinations,$i,1);
			}
			return $this;
		}

		/**
		 * @param IMessage $message
		 * @return $this
		 */
		public function setMessage(IMessage $message){
			$this->message = $message;
		}

		/**
		 * @return IMessage
		 */
		public function getMessage(){
			return $this->message;
		}

		/**
		 * @return IContact[]
		 */
		public function getDestinations(){
			return $this->destinations;
		}
	}
}

