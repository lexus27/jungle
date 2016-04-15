<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.04.2016
 * Time: 14:49
 */
namespace Jungle\Util\Data\Map {

	/**
	 * Interface DataMapCollectionInterface
	 * @package Jungle\Data
	 */
	interface DataMapCollectionInterface{


		public function setSchema($schema);

		public function getSchema();

		public function getFieldNames();

		public function getPrimaryKey();


		public function valueAccessGet($item, $field);

		public function valueAccessSet($item, $field, $value);


		public function setSortFields($fields);

		public function getSortFields();


		public function setContainCriteria($criteria);

		public function getContainCriteria();

	}
}

