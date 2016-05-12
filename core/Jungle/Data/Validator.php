<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 18:59
 */
namespace Jungle\Data {

	use Jungle\Basic\INamed;
	use Jungle\Data\Validator\ValidatorInterface;
	use Jungle\Util\Value\Callback;

	/**
	 * Class DataMapValidator
	 */
	class Validator implements ValidatorInterface, INamed{

		protected $error_message;

		protected $name;

		/**
		 * @param ValidatorInterface|callable|null $validator
		 * @return callable|ValidatorInterface|null
		 */
		public static function checkoutValidator(callable $validator = null){
			return Callback::checkoutCallableInstance('Validator',ValidatorInterface::class,$validator);
		}

		/**
		 * @param $property
		 * @return string
		 */
		public function getMessage($property = null){
			if($property==null){
				$property = '{anonymous_property}';
			}
			return str_replace(['[[property]]','[[validator_type]]'],[$property,$this->getName()],$this->error_message);
		}

		/**
		 * @param array $options
		 */
		public function __construct(array $options = []){
			if(isset($options['message'])){
				$this->setErrorMessage($options['message']);
			}
		}

		public function setErrorMessage($error_message){
			$this->error_message  = $error_message;
			return $this;
		}

		/**
		 * @param $value
		 * @param $property
		 * @return bool
		 */
		public function check($value,$property = null){
			return true;
		}

		/**
		 * @param $value
		 * @param null $property
		 * @return array|bool|string
		 */
		public function __invoke($value,$property=null){
			return $this->check($value,$property);
		}

		/**
		 * Получить имя объекта
		 * @return string
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
			$this->name = $name;
			return $this;
		}

	}
}

