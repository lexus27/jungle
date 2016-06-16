<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:26
 */
namespace Jungle\Data\Foundation\Record {

	use Jungle\Data\Foundation\Record\Head\Schema;


	/**
	 * Interface CollectionInterface
	 * @package Jungle\Data\Foundation\Record
	 */
	interface CollectionInterface{

		/**
		 * @param Schema $schema
		 * @return $this
		 */
		public function setSchema(Schema $schema);

		/**
		 * @return Schema
		 */
		public function getSchema();

		/**
		 * @return array
		 */
		public function getFieldNames();

	}
}

