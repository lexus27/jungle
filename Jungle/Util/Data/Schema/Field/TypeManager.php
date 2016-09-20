<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.07.2016
 * Time: 23:44
 */
namespace Jungle\Util\Data\Schema\Field {

	/**
	 * Class TypeManager
	 * @package Jungle\Util\Data\Schema\Field
	 */
	class TypeManager{

		protected $types = [];


		/**
		 * @param Type $type
		 * @return $this
		 */
		public function addType(Type $type){
			if(!in_array($type, $this->types, true)){
				$this->types[] = $type;
			}
			return $this;
		}

		public function getType($name){
			foreach($this->types as $type){

			}
		}

	}
}

