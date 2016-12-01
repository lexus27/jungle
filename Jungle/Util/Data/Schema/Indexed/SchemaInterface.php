<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 18:56
 */
namespace Jungle\Util\Data\Schema\Indexed {

	/**
	 * Interface SchemaInterface
	 * @package Jungle\Data\Schema\Indexed
	 */
	interface SchemaInterface extends SchemaMetaInterface{

		/**
		 * @return FieldInterface
		 */
		public function getPkField();

		/**
		 * @param FieldInterface|string $field
		 * @return bool
		 */
		public function isPrimaryField($field);

		/**
		 * @param FieldInterface|string $field
		 * @return bool
		 */
		public function isUniqueField($field);

		/**
		 * @param $name
		 * @return IndexInterface|null
		 */
		public function getIndex($name);

		/**
		 * @return IndexInterface[]
		 */
		public function getIndexes();
	}
}

