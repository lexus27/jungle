<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.10.2016
 * Time: 13:22
 */
namespace Jungle\Util\Buffer {
	
	use Jungle\Util\Buffer;

	/**
	 * Class Simple
	 * @package Jungle\Util\Buffer
	 */
	class Simple extends Buffer{

		/** @var  string */
		protected $data;

		/**
		 * @param $string
		 * @return mixed
		 */
		public function write($string){
			$this->data.=$string;
		}

		/**
		 * @return void
		 */
		public function clear(){
			$this->data = '';
		}

		/**
		 * @return mixed
		 */
		public function contents(){
			return $this->data;
		}
	}
}

