<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 13:35
 */
namespace Jungle\Util\Data {
	
	use Jungle\Util\Data\Validation\MessageInterface;
	use Jungle\Util\Data\Validation\Validator;
	
	/**
	 * Class Validation
	 * @package Jungle\Util\Data
	 */
	class Validation{

		/** @var Validator[]  */
		protected $validators = [];

		/**
		 * @param Validator $validator
		 */
		public function addValidator(Validator $validator){
			$this->validators[] = $validator;
		}
		
		/**
		 * @param $object
		 */
		public function validate($object){

			foreach($this->validators as $validator){
				$result = $validator->validate($object);
				if($result instanceof MessageInterface){

				}

			}

		}

		/**
		 *
		 */
		public function getMessages(){

		}

	}
}

