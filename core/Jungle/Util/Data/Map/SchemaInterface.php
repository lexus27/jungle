<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.04.2016
 * Time: 18:43
 */
namespace Jungle\Util\Data\Map {

	interface SchemaInterface{

		public function addField($field);

		public function searchField($field);

		public function removeField($field);

		public function addIndex($index);

		public function searchIndex($index);

		public function removeIndex($index);


		public function getPrimary();

		public function getFields();
		
	}
}

