<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 18:53
 */
namespace Jungle\Util\Data\Foundation\Schema\OuterInteraction {


	/**
	 * Class FieldOuterInteraction
	 * @package modelX
	 *
	 * Поле знающее как получить данные из внешнего объекта данных array|object|...etc
	 *
	 * @method Schema getSchema()
	 */
	abstract class Field extends \Jungle\Util\Data\Foundation\Schema\Field implements ValueAccessAwareInterface{

		/**
		 * @return array|callable
		 */
		public function getSetter(){
			return ValueAccessor::getDefaultSetter();
		}

		/**
		 * @return array|callable
		 */
		public function getGetter(){
			return ValueAccessor::getDefaultGetter();
		}

		/**
		 * @return string
		 */
		protected function _getOuterInteractionKey(){
			return $this->name;
		}

		/**
		 * @param $data
		 * @param $key
		 * @return mixed|null
		 */
		public function valueAccessGet($data, $key){
			if(!$data){
				return $this->getDefault();
			}
			$value = ValueAccessor::handleGetter($this->getGetter(),$data,$this->_getOuterInteractionKey(),[$this]);
			return $this->evaluate($value);

		}

		/**
		 * @param $data
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		public function valueAccessSet($data, $key, $value){
			return ValueAccessor::handleSetter(
				$this->getSetter(),
				$data,
				$this->_getOuterInteractionKey(),
				$this->originate($value),
				[$this]
			);
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function valueAccessExists($key){
			return $this->name === $key;
		}

	}

}

