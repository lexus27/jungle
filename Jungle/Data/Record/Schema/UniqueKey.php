<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 16.11.2016
 * Time: 5:22
 */
namespace Jungle\Data\Record\Schema {

	use Jungle\Data\Record;
	use Jungle\Util\Value\String;

	/**
	 * Class UniqueKey
	 * @package Jungle\Data\Record\Schema
	 */
	class UniqueKey{

		/** @var  string|null */
		public $name;

		/** @var array  */
		public $field_list = [];

		/**
		 * UniqueKey constructor.
		 * @param $field_list
		 * @param null $name
		 */
		public function __construct(array $field_list, $name = null){
			$this->name = $name;
			$this->field_list = $field_list;
		}

		/**
		 * @param Record $record
		 * @return string
		 */
		public function fetchId(Record $record){
			$data = array_values($record->getProperties($this->field_list));
			return String::crc32Alnum(implode($data));
		}

	}
}

