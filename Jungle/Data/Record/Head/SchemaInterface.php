<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:30
 */
namespace Jungle\Data\Record\Head {
	
	/**
	 * Interface SchemaInterface
	 * @package modelX
	 */
	interface SchemaInterface{

		/**
		 * @return string
		 */
		public function getSource();

		/**
		 * @return mixed
		 * db, fs, api , etc...
		 */
		public function getSourceType();

		/**
		 * @return array
		 */
		public function getSourceFieldNames();

		/**
		 * @return string
		 */
		public function getSourcePrimaryName();

	}
}

