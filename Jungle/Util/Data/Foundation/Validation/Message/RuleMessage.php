<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 17:34
 */
namespace Jungle\Util\Data\Foundation\Validation\Message {
	
	use Jungle\Util\Data\Foundation\Validation\Message;

	/**
	 * Class RuleMessage
	 * @package Jungle\Util\Data\Foundation\Validation\Message
	 */
	class RuleMessage extends Message implements RuleMessageInterface{

		/** @var array  */
		protected $params = [];


		/**
		 * RuleMessage constructor.
		 * @param $type
		 * @param $params
		 */
		public function __construct($type, $params){
			$this->type = $type;
			$this->params = $params;
		}

		/**
		 * @return array
		 */
		public function getParams(){
			return $this->params;
		}

	}
}

