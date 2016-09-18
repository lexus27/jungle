<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 13:57
 */
namespace Jungle\Util\Data\Foundation\Schema\Validation\Validator {
	
	use Jungle\Util\Data\Foundation\Record\PropertyRegistryInterface;
	use Jungle\Util\Data\Foundation\Schema\Validation\MessageInterface;
	use Jungle\Util\Data\Foundation\Schema\Validation\Validator;

	/**
	 * Class PresenceOf
	 * @package Jungle\Util\Data\Foundation\Schema\Validation\Validator
	 */
	class PresenceOf extends Validator{

		protected $min_length = null;

		protected $max_length = null;

		/**
		 * @param PropertyRegistryInterface $object
		 * @return false|MessageInterface
		 */
		public function validate(PropertyRegistryInterface $object){
			// TODO: Implement validate() method.
		}
	}
}

