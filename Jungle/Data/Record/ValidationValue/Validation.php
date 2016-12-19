<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.11.2016
 * Time: 13:13
 */
namespace Jungle\Data\Record\ValidationValue {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Validation\ValidationCollector;

	/**
	 * Class Validation
	 * @package Jungle\Data\Record\Validation
	 */
	abstract class Validation extends Record\Validation\ValidationRule{

		abstract function validate($field_name, $value, ValidationCollector $collector);

	}
}

