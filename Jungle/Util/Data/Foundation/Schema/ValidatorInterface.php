<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.07.2016
 * Time: 1:03
 */
namespace Jungle\Util\Data\Foundation\Schema {
	
	use Jungle\Util\Data\Foundation\Record\PropertyRegistryInterface;

	/**
	 * Interface ValidatorInterface
	 * @package Jungle\Util\Data\Foundation\Schema
	 */
	interface ValidatorInterface{

		/**
		 * @param PropertyRegistryInterface $object
		 * @return mixed
		 */
		public function validate(PropertyRegistryInterface $object);

	}
}

