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
	 * Class SchemaMappedOuterInteraction
	 * @package modelX
	 *
	 * @property Field[]    $fields
	 *
	 * @method Field        getField($key)
	 * @method Field[]      getFields()
	 * @method Field        getPrimaryField()
	 *
	 */
	abstract class Schema extends \Jungle\Util\Data\Schema\OuterInteraction\Schema{

		protected $original_names;

		/**
		 * @param $field
		 */
		protected function beforeAddField($field){
			if(!$field instanceof Field){
				throw new \LogicException();
			}
		}

		/**
		 * @return string[]
		 */
		public function getOriginalNames(){
			$this->_initCache();
			return $this->original_names;
		}

		protected function _initCache(){
			$original_names_fill = $this->original_names===null;
			if($original_names_fill) $this->original_names = [];
			if($original_names_fill){
				foreach($this->getFields() as $field){
					if($original_names_fill && $field instanceof Field){
						$this->original_names[] = $field->getOriginalKey();
					}
				}
			}
		}


		/**
		 * @return string
		 */
		public function getOriginalPrimaryName(){
			return $this->getPrimaryField()->getOriginalKey();
		}

	}

}

