<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 17:15
 */
namespace Jungle\Util\Data\Foundation\Validation {

	use Jungle\Util\Data\Foundation\Validation\Message\MessageInterface;

	/**
	 * Class Message
	 * @package Jungle\Util\Data\Foundation\Validation
	 */
	abstract class Message implements MessageInterface{

		/** @var  string */
		protected $type;

		/**
		 * Message constructor.
		 * @param $type
		 */
		public function __construct($type){
			$this->type = $type;
		}

		/**
		 * @return string
		 */
		public function getType(){
			return $this->type;
		}

	}
}

