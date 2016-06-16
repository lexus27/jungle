<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 18:56
 */
namespace Jungle\Data\Foundation\Schema {

	/**
	 * Interface SchemaMetaInterface
	 * @package Jungle\Data\Foundation\Schema
	 */
	interface SchemaMetaInterface{

		/**
		 * @return string[]
		 */
		public function getFieldNames();

	}
}

