<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 18:07
 */
namespace Jungle\Util\Data\Foundation\Schema {

	/**
	 * Class SchemaInterface
	 * @package Jungle\Util\Data\Foundation\Schema
	 */
	interface SchemaInterface{

		/**
		 * @param $name
		 * @return FieldInterface
		 */
		public function getField($name);

		/**
		 * @param FieldInterface|string $field
		 * @return mixed
		 */
		public function getFieldIndex($field);

		/**
		 * @param $index
		 * @return FieldInterface|null
		 */
		public function getFieldByIndex($index);

		/**
		 * @return string[]|int[]
		 */
		public function getFieldNames();

		/**
		 * @return FieldInterface[]
		 */
		public function getFields();

	}
}

