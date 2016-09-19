<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.09.2016
 * Time: 12:53
 */
namespace Jungle\Util\Data\Foundation\Validation\Message {

	/**
	 * Class MessageTrait
	 * @package Jungle\Util\Data\Foundation\Validation\Message
	 */
	trait MessageTrait{

		/** @var  string */
		protected $type;

		/**
		 * @return string
		 */
		public function getType(){
			return $this->type;
		}


	}
}
