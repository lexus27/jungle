<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.04.2016
 * Time: 22:33
 */
namespace Jungle\RegExp\Template {

	use Jungle\Basic\INamed;

	/**
	 * Реализация плейсхолдера
	 *
	 * Class Placeholder
	 * @package Jungle\RegExp\Template
	 */
	class Placeholder implements INamed{

		/** @var  string */
		protected $name;

		/** @var  string */
		protected $type;

		/** @var  string */
		protected $pattern;

		/**
		 * Получить имя объекта
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * Выставить имя объекту
		 * @param string $name
		 * @return $this
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}


		/**
		 * @param string $type
		 * @return $this
		 */
		public function setType($type){
			$this->type = $type;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getType(){
			return $this->type;
		}


		/**
		 * @param string $pattern
		 * @return $this
		 */
		public function setPattern($pattern){
			$this->pattern = $pattern;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getPattern(){
			return $this->pattern;
		}

	}
}

