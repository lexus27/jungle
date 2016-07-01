<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 05.05.2015
 * Time: 2:30
 */

namespace Jungle\XPlate\CSS {

	use Jungle\Util\INamed;
	use Jungle\XPlate\CSS\Media\Query;

	/**
	 * Class Media
	 * @package Jungle\XPlate\CSS
	 */
	class Media extends RuleSpace implements INamed{

		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var Query[]
		 */
		protected $queries = [];

		/**
		 * @var Document
		 */
		protected $document_owner;

		/**
		 * Получить имя объекта
		 * @return mixed
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * Выставить имя объекту
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			if(!$name)throw new \InvalidArgumentException('Name is empty');
			$this->name = $name;
			return $this;
		}

	}

}