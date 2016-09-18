<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 14:00
 */
namespace Jungle\Util\Data\Foundation\Schema\Validation\Validator {
	
	use Jungle\Util\Data\Foundation\Record\PropertyRegistryInterface;
	use Jungle\Util\Data\Foundation\Schema\Validation\MessageInterface;
	use Jungle\Util\Data\Foundation\Schema\Validation\Validator;

	/**
	 * Class RegExpPattern
	 * @package Jungle\Util\Data\Foundation\Schema\Validation\Validator
	 */
	class RegExpPattern extends Validator{

		protected $type = 'Regexp';

		/** @var   */
		protected $pattern;


		/**
		 * @param PropertyRegistryInterface $object
		 * @return false|MessageInterface
		 */
		public function validate(PropertyRegistryInterface $object){

		}
	}
}

