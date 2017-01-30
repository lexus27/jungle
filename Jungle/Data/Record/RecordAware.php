<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 25.07.2016
 * Time: 0:13
 */
namespace Jungle\Data\Record {
	
	use Jungle\Data\Record;

	/**
	 * Class RecordAware
	 * @package Jungle\Data\Record\Exception
	 */
	interface RecordAware{

		/**
		 * @return Record
		 */
		public function getRecord();

	}
}

