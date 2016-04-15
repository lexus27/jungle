<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.04.2016
 * Time: 18:48
 */
namespace Jungle\Util\Data\Map {

	interface FieldInterface{

		public function getName();

		public function getType();

		public function getValueType();

		public function getDefaultValue();

		public function isNullable();

		public function isPrivate();

		public function isReadonly();



	}
}

