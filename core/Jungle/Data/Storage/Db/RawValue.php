<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.06.2016
 * Time: 19:55
 */
namespace Jungle\Data\Storage\Db {

	/**
	 * Class RawValue
	 * @package Jungle\Data\Storage\Db
	 */
	class RawValue{

		/** @var  mixed */
		protected $value;

		/**
		 * RawValue constructor.
		 * @param $value
		 */
		public function __construct($value){
			$this->value = $value;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function __toString(){
			return $this->value;
		}

		/**
		 * @return mixed
		 */
		public function getValue(){
			return $this->value;
		}

	}
}

