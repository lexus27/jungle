<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 14:10
 */
namespace Jungle\Util\Data\Validation\Message {

	use Jungle\Util\Data\Validation\Message;

	/**
	 * Class ExpertizeMessage
	 * @package Jungle\Util\Data\Validation\Message
	 */
	abstract class ExpertizeMessage extends Message implements ExpertizeMessageInterface{

		/** @var array  */
		protected $params = [];

		/**
		 * RuleMessage constructor.
		 * @param string $type
		 * @param string $params
		 * @param string $systemMessage
		 */
		public function __construct($type, $params, $systemMessage = 'Unexpected Data'){
			parent::__construct($type, $systemMessage);
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

