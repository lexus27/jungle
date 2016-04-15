<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.03.2016
 * Time: 1:57
 */
namespace Jungle\Data\DataMap\Sorter {

	use Jungle\Data\DataMap\Schema;
	use Jungle\Data\Sorter\SorterInterface as SimpleSorterInterface;

	/**
	 * Interface SorterInterface
	 * @package Jungle\Data\DataMap
	 */
	interface SorterInterface extends SimpleSorterInterface{

		/**
		 * @param $fields
		 * @return $this
		 */
		public function setSortFields($fields);

		/**
		 * @return array|null
		 */
		public function getSortFields();

		/**
		 * @param Schema $schema
		 * @return $this
		 */
		public function setSchema(Schema $schema);

		/**
		 * @return Schema
		 */
		public function getSchema();

	}
}

