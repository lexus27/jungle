<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.05.2016
 * Time: 20:27
 */
namespace Jungle\Util\Data\Condition {

	/**
	 * Interface ConditionBlockInterface
	 * @package Jungle\Util\Data\Condition
	 */
	interface ConditionBlockInterface{

		const OPERATOR_AND = 'AND';
		const OPERATOR_OR  = 'OR';

		/**
		 * @param ConditionInterface $condition
		 * @param null $operator
		 * @return $this
		 */
		public function addCondition(ConditionInterface $condition, $operator = null);

	}
}

