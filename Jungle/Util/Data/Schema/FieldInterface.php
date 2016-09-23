<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 18:55
 */
namespace Jungle\Util\Data\Schema {

	/**
	 * Interface FieldInterface
	 * @package Jungle\Util\Data\Schema
	 */
	interface FieldInterface{

		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @return string
		 */
		public function getType();

		/**
		 * @return mixed
		 */
		public function getDefault();

		/**
		 * @return bool
		 */
		public function isNullable();

		/**
		 * @return SchemaInterface
		 */
		public function getSchema();

		/**
		 * @param SchemaInterface $schema
		 * @return mixed
		 */
		public function setSchema(Schema $schema);

	}
}

