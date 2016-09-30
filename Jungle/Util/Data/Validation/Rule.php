<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 15:06
 */
namespace Jungle\Util\Data\Validation {

	/**
	 * Class Rule
	 * @package Jungle\Util\Data\Schema\ValueType
	 */
	abstract class Rule extends ExpertizeAbstract implements ValueCheckerInterface{

		/**
		 * @param $value
		 * @param array $parameters
		 * @return bool
		 */
		public function check($value,array $parameters = []){
			return $this->expertize($value, $parameters);
		}

		/**
		 * @param $result
		 * @return \Jungle\Util\Data\Validation\MessageInterface
		 */
		protected function _prepareMessage($result){
			return new Message\RuleMessage($this->type,$this->getParams());
		}


	}
}

