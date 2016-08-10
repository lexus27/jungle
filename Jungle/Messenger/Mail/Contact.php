<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.12.2015
 * Time: 22:49
 */
namespace Jungle\Messenger\Mail {


	/**
	 * Class Destination
	 * @package Jungle\Messenger\Mail
	 */
	class Contact implements IContact{

		/** @var  int */
		protected $type = self::TYPE_MAIN;

		/** @var  string */
		protected $name;

		/** @var  string */
		protected $address;

		/**
		 * @param int $type
		 * @return $this
		 */
		public function setType($type = self::TYPE_MAIN){
			$this->type = $type;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getType(){
			return $this->type;
		}

		/**
		 * @param string $name
		 * @return $this
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param mixed $address
		 * @return $this
		 */
		public function setAddress($address){
			$this->address = $address;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getAddress(){
			return $this->address;
		}

		/**
		 * @param $contact
		 * @return Contact
		 */
		public static function getContact($contact){
			if($contact instanceof IContact){
				return $contact;
			}elseif(is_string($contact) && preg_match('@(.+)?<(.+)>@',$contact,$m)){
				$contact = new Contact();
				$contact->setAddress(trim($m[2]));
				if($m[1])$contact->setName(trim($m[1]));
				return $contact;
			}elseif(is_array($contact) && isset($contact['address']) && $contact['address']){
				$contact = new Contact();
				$contact->setAddress($contact['address']);
				if(isset($contact['name']))$contact->setName($contact['name']);
				return $contact;
			}else{
				throw new \LogicException('Contact("'.$contact.'") invalid definition');
			}
		}
		
	}
}

