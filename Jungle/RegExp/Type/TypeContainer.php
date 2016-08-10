<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.04.2016
 * Time: 23:52
 */
namespace Jungle\RegExp\Type {

	use Jungle\RegExp\Type;

	/**
	 * Class TypeContainer
	 * @package Jungle\RegExp\Template
	 */
	class TypeContainer extends Type{

		use TypeAggregateTrait;

		/**
		 * @param Type $type
		 */
		protected function afterCreate(Type $type){
			$type->setRegistry($this->registry);
			$type->setParent($this);
		}
	}
}

