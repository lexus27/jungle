<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.07.2016
 * Time: 2:37
 */
namespace Jungle\User\OldSession\SignatureProvider {

	use Jungle\Di\DiInterface;
	use Jungle\User\OldSession\SignatureProvider;

	/**
	 * Class CustomProvider
	 * @package Jungle\User\OldSession\SignatureProvider
	 */
	class CustomProvider extends SignatureProvider{

		/** @var callable  */
		protected $getter_function;

		/** @var callable  */
		protected $setter_function;

		/** @var callable  */
		protected $remove_function;

		/**
		 * CustomProvider constructor.
		 * @param DiInterface $di
		 * @param $getter_function
		 * @param $setter_function
		 * @param $remove_function
		 */
		public function __construct(DiInterface $di, callable $getter_function, callable $setter_function, callable $remove_function){
			$this->setDi($di);
			$this->getter_function = $getter_function;
			$this->setter_function = $setter_function;
			$this->remove_function = $remove_function;
		}

		/**
		 * @return mixed
		 */
		public function getSignature(){
			return call_user_func($this->getter_function,$this);
		}

		/**
		 * @param $signature
		 * @return $this
		 */
		public function setSignature($signature){
			call_user_func($this->setter_function,$this,$signature);
			return $this;
		}

		/**
		 * @return $this
		 */
		public function removeSignature(){
			call_user_func($this->remove_function,$this);
			return $this;
		}

	}
}

