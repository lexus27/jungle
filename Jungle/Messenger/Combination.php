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
	class Combination implements CombinationInterface{

		/**
		 * @var ContactInterface[]
		 */
		protected $destinations = [];

		/**
		 * @var MessageInterface
		 */
		protected $message;

		/**
		 * @param ContactInterface $destination
		 * @return $this
		 */
		public function addDestination(ContactInterface $destination){
			if($this->searchDestination($destination)===false){
				$this->destinations[] = $destination;
			}
			return $this;
		}

		/**
		 * @param ContactInterface $destination
		 * @return int|bool
		 */
		public function searchDestination(ContactInterface $destination){
			return array_search($destination,$this->destinations,true);
		}

		/**
		 * @param ContactInterface $destination
		 * @return $this
		 */
		public function removeDestination(ContactInterface $destination){
			if(($i = $this->searchDestination($destination))!==false){
				array_splice($this->destinations,$i,1);
			}
			return $this;
		}

		/**
		 * @param MessageInterface $message
		 * @return $this
		 */
		public function setMessage(MessageInterface $message){
			$this->message = $message;
		}

		/**
		 * @return MessageInterface
		 */
		public function getMessage(){
			return $this->message;
		}

		/**
		 * @return ContactInterface[]
		 */
		public function getDestinations(){
			return $this->destinations;
		}
	}
}

