<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 11.09.2016
 * Time: 10:45
 */
namespace Jungle\Di {

	use Jungle\Util\Data\Registry\RegistryReadInterface;

	/**
	 * Interface DiLocatorInterface
	 * @package Jungle\Di
	 */
	interface DiLocatorInterface extends RegistryReadInterface{

		/**
		 * @param $serviceKey
		 * @return mixed
		 */
		public function getShared($serviceKey);

		/**
		 * @param $key
		 * @param array|null $parameters
		 * @return mixed
		 */
		public function get($key,array $parameters = null);




		/**
		 * @param $name
		 * @return ServiceInterface
		 */
		public function getService($name);

		/**
		 * @param $name
		 * @return DiInterface
		 */
		public function getServiceContainer($name);

		/**
		 * @param $object
		 * @return mixed
		 */
		public function getSharedServiceBy($object);





		/**
		 * @return DiInterface[]|ServiceInterface[]
		 */
		public function getServices();

		/**
		 * @return array
		 */
		public function getServiceNames();

		/**
		 * @return array
		 */
		public function getContainerNames();

	}
}

