<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.05.2016
 * Time: 20:27
 */
namespace Jungle\Data\Foundation\Condition {

	/**
	 * Interface ConditionTargetInterface
	 * @package Jungle\Data\Foundation\Condition
	 */
	interface ConditionTargetInterface extends ConditionInterface{

		/**
		 * @param string $name
		 * @return $this
		 */
		public function setField($name);

		/**
		 * @param string $operator_definition
		 * @return $this
		 */
		public function setOperator($operator_definition);

		/**
		 * @param $wanted
		 * @return $this
		 */
		public function setWanted($wanted);

	}
}

