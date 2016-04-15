<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 11.05.2015
 * Time: 15:17
 */

namespace Jungle\XPlate\CSS\Selector\AttributeQuery {

	use Jungle\Basic\INamed;

	/**
	 * Interface IChecker
	 * @package Jungle\XPlate\CSS\Selector\AttributeQuery
	 */
	interface IChecker extends INamed{

		/**
		 * @param mixed $value Поданное значение, существуемое
		 * @param mixed $collated Требуемое значение для сопоставления
		 * @return bool
		 */
		public function check($value, $collated);

		/**
		 * @param $value
		 * @param $collated
		 * @return mixed
		 */
		public function __invoke($value, $collated);

	}
}