<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:50
 */
namespace Jungle\Util\Data\Schema\OuterInteraction\Mapped {
	
	/**
	 * Class FieldMappedOuterInteraction
	 * @package modelX
	 *
	 * Получение данных с использованием оригинального ключа
	 *
	 */
	abstract class Field extends \Jungle\Util\Data\Schema\OuterInteraction\Field{

		/** @var string|null */
		protected $original_key;

		/**
		 * @param $key
		 * @return $this
		 */
		public function setOriginalKey($key){
			$this->original_key = $key;
			return $this;
		}


		/**
		 * @return int|null|string
		 */
		public function getOriginalKey(){
			return $this->original_key;
		}

		/**
		 * @return string
		 */
		protected function _getOuterInteractionKey(){
			if($this->original_key){
				return $this->original_key;
			}
			return parent::_getOuterInteractionKey();
		}


	}

}

