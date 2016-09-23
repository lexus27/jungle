<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 14:26
 */
namespace Jungle\Util\Data\Validation {

	/**
	 * Class ExpertizeAggregator
	 * @package Jungle\Util\Data\Validation
	 */
	class ExpertizeAggregator{

		/** @var  ExpertizeInterface[]  */
		protected $expertize_collection = [];

		/**
		 * @param $value
		 * @return MessageInterface[]
		 */
		public function aggregateExpertize($value){
			$messages = [];
			foreach($this->expertize_collection as $expertize){
				$result = $expertize->expertize($value);
				if($result instanceof MessageInterface){
					$messages[] = $result;
				}
			}
			return $messages;
		}

	}
}

