<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 06.01.2017
 * Time: 0:27
 */
namespace Jungle\Util\Lexr {

	/**
	 * Class Context
	 * @package Jungle\Util\Lexr
	 */
	class Context{

		public $string;

		public $length;

		public function hasBefore($position, $needle, $offset = 0, $ignore = null){
			return substr($this->string,$position-$offset,$needle) === $needle;
		}
		public function hasAfter($position, $needle, $offset = 0, $ignore = null){
			return substr($this->string,$position+$offset,$needle) === $needle;
		}


		public function getBefore($position, $length, $offset = 0, $ignore = null){
			return substr($this->string,$position-$offset,$length);
		}
		public function getAfter($position, $length, $offset = 0, $ignore = null){
			return substr($this->string,$position+$offset,$length);
		}


		public function countBefore($position, $subject, $offset = 0, $ignore = null){
			$subject_length = strlen($subject);
			for($i=0;substr($this->string,$position-($offset+$i),$subject_length)===$subject;$i++){}
			return $i;
		}
		public function countAfter($position, $subject, $offset = 0, $ignore = null){
			$subject_length = strlen($subject);
			for($i=0;substr($this->string,$position+($offset+$i),$subject_length)===$subject;$i++){}
			return $i;
		}






	}
}

