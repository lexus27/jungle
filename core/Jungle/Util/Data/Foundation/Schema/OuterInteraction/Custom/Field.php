<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:52
 */
namespace Jungle\Util\Data\Foundation\Schema\OuterInteraction\Custom {

	use Jungle\Util\Data\Foundation\Schema\OuterInteraction\ValueAccessor;
	use Jungle\Util\Data\Foundation\Schema\OuterInteraction\ValueAccessor\GetterInterface;
	use Jungle\Util\Data\Foundation\Schema\OuterInteraction\ValueAccessor\SetterInterface;
	
	/**
	 * Class FieldCustomOriginalOuterInteraction
	 * @package modelX
	 *
	 * Кастомное получение данных из оригинала посредством пользовательской функции Getter|Setter
	 */
	abstract class Field extends \Jungle\Util\Data\Foundation\Schema\OuterInteraction\Mapped\Field{

		/** @var  callable|array|null */
		protected $setter;

		/** @var  callable|array|null */
		protected $getter;

		/**
		 * @param $setter
		 * @return $this
		 */
		public function setSetter($setter){
			$this->setter = ValueAccessor::checkoutSetter($setter);
			return $this;
		}

		/**
		 * @param $getter
		 * @return $this
		 */
		public function setGetter($getter){
			$this->getter = ValueAccessor::checkoutGetter($getter);
			return $this;
		}

		/**
		 * @return SetterInterface|array|callable
		 */
		public function getSetter(){
			if($this->setter){
				return $this->setter;
			}
			return parent::getSetter();
		}

		/**
		 * @return GetterInterface|array|callable
		 */
		public function getGetter(){
			if($this->getter){
				return $this->getter;
			}
			return parent::getGetter();
		}

	}
}

