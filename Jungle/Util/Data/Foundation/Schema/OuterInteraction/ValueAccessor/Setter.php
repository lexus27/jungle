<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 19:00
 */
namespace Jungle\Util\Data\Foundation\Schema\OuterInteraction\ValueAccessor {

	use Jungle\Util\Data\Foundation\Record\Properties\PropertyRegistryInterface;

	/**
	 * Class Setter
	 * @package Jungle\Util\Data\Foundation\Schema\OuterInteraction\ValueAccessor
	 */
	class Setter implements SetterInterface{

		/**
		 * @param $data
		 * @param $key
		 * @param $value
		 * @return array|\ArrayAccess
		 */
		public function __invoke($data, $key, $value){
			if($data === null){
				return [$key => $value];
			}
			if($data instanceof PropertyRegistryInterface){
				$data->setProperty($key, $value);
			}elseif(is_array($data) || $data instanceof \ArrayAccess){
				$data[$key] = $value;
			}elseif(is_object($data)){
				$data->{$key} = $value;
			}else{
				throw new \LogicException('[OuterSetter] Wrong data type');
			}
			return $data;
		}

	}
}

