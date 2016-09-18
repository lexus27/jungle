<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 25.07.2016
 * Time: 4:04
 */
namespace Jungle\Util\Data\Foundation\Schema {

	/**
	 * Interface ValueTypeInterface
	 * @package Jungle\Util\Data\Foundation\Schema
	 */
	interface ValueTypeInterface{

		/**
		 * @param $evaluated_value
		 * @param array $options
		 * @return bool
		 */
		public function validate($evaluated_value,array $options = null);

		/**
		 * @param $passed_evaluated_value
		 * @param array $options
		 * @return mixed
		 */
		public function stabilize($passed_evaluated_value,array $options = null);

		/**
		 * @param $raw_value
		 * @param array $options
		 * @return mixed
		 */
		public function evaluate($raw_value,array $options = null);

		/**
		 * @param $evaluated_value
		 * @param array $options
		 * @return mixed|string
		 */
		public function originate($evaluated_value,array $options = null);

	}
}

