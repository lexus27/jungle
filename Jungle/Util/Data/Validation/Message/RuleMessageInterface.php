<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 17:32
 */
namespace Jungle\Util\Data\Validation\Message {

	/**
	 * Interface RuleMessageInterface
	 * @package Jungle\Util\Data\Validation\Message
	 */
	interface RuleMessageInterface extends ExpertizeMessageInterface{

		/**
		 * @return array
		 */
		public function getParams();

	}
}

