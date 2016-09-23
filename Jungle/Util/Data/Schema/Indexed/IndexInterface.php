<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 18:55
 */
namespace Jungle\Util\Data\Schema\Indexed {

	/**
	 * Interface IndexInterface
	 * @package Jungle\Util\Data\Schema
	 */
	interface IndexInterface{

		const TYPE_PRIMARY = 1;

		const TYPE_UNIQUE = 2;

		const TYPE_KEY = 3;

		const TYPE_SPATIAL = 4;

		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @return string
		 */
		public function getType();

		/**
		 * @return array
		 */
		public function getFieldNames();

		/**
		 * @param string $field_name
		 * @return int|null
		 */
		public function getFieldSize($field_name);

		/**
		 * @param string $field_name
		 * @return int|null
		 */
		public function getFieldDirection($field_name);

		/**
		 * @param string $field_name
		 * @return bool
		 */
		public function hasField($field_name);

	}
}

