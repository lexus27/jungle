<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.11.2016
 * Time: 15:22
 */
namespace Jungle\Data\Record\Validation {
	
	use Jungle\Data\Record;

	class CheckEmail extends CheckPattern{

		/**
		 * CheckEmail constructor.
		 * @param $fields
		 * @param null $pattern
		 */
		public function __construct($fields,$pattern = null){
			$pattern = $pattern?: '@[[:alpha:]][\w\-\_]*@[[:alpha:]]\w*\.[[:alpha:]]\w*@';
			parent::__construct($pattern, $fields);
		}


		function validate(Record $record, ValidationCollector $collector){
			// TODO: Implement validate() method.
		}
	}
}

