<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 19:00
 */
namespace Jungle\Util\Data\Schema\OuterInteraction\ValueAccessor {

	use Jungle\Util\Data\Record\Properties\PropertyRegistryInterface;

	/**
	 * Class Getter
	 * @package Jungle\Util\Data\Schema\OuterInteraction\ValueAccessor
	 */
	class Getter implements GetterInterface{

		/**
		 * @param $data
		 * @param $key
		 * @return mixed|null
		 */
		public function __invoke($data, $key){
			if($data === null){
				return null;
			}
			if($data instanceof PropertyRegistryInterface){
				return $data->getProperty($key);
			}elseif(is_array($data) || $data instanceof \ArrayAccess){
				return $data[$key];
			}elseif(is_object($data)){
				return $data->{$key};
			}else{
				throw new \LogicException('[OuterGetter] Wrong data type');
			}
		}

	}
}

