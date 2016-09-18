<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 13:58
 */
namespace Jungle\Util\Data\Foundation\Schema\Validation\Validator {
	
	use Jungle\Util\Data\Foundation\Record\PropertyRegistryInterface;
	use Jungle\Util\Data\Foundation\Schema\Validation\MessageInterface;
	use Jungle\Util\Data\Foundation\Schema\Validation\Validator;

	/**
	 * Class NumberRange
	 * @package Jungle\Util\Data\Foundation\Schema\Validation\Validator
	 */
	class NumberRange extends Validator{

		protected $min;

		protected $max;

		/**
		 * @param PropertyRegistryInterface $object
		 * @return false|MessageInterface
		 */
		public function validate(PropertyRegistryInterface $object){
			// TODO: Implement validate() method.
		}
	}
}

