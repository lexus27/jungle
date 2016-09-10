<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.04.2016
 * Time: 23:52
 */
namespace Jungle\Di {

	use Jungle\Util\Data\Foundation\Registry\RegistryInterface;
	use Jungle\Util\Data\Foundation\Registry\RegistryRemovableInterface;

	/**
	 * Interface DiInterface
	 * @package Jungle\Di
	 */
	interface DiInterface extends RegistryInterface, RegistryRemovableInterface, \ArrayAccess{

		/**
		 * @return array
		 */
		public function getServiceNames();

		/**
		 * @return array
		 */
		public function getContainerNames();



		/**
		 * @return DiInterface
		 */
		public function getRoot();

		/**
		 * @param DiInterface $parent
		 * @return mixed
		 */
		public function setParent(DiInterface $parent);

		/**
		 * @return mixed
		 */
		public function getParent();



		/**
		 * @return DiInterface
		 */
		public function getNext();

		/**
		 * @param DiInterface $di
		 * @return mixed
		 */
		public function setNext(DiInterface $di);




		/**
		 * @param $existingServiceKey
		 * @param null $definition
		 * @return $this
		 */
		public function setOverlapFrom($existingServiceKey, $definition = null);

		/**
		 * @param bool|false|string $overlap
		 * @return $this
		 */
		public function useSelfOverlapping($overlap = false);

		/**
		 * @return bool
		 */
		public function isSelfOverlapping();

		/**
		 * @return mixed
		 */
		public function getOverlapKey();



		/**
		 * @param $key
		 * @param $definition
		 * @param bool|false $shared
		 * @return mixed
		 */
		public function set($key, $definition, $shared = false);

		/**
		 * @param $key
		 * @param array|null $parameters
		 * @return mixed
		 */
		public function get($key,array $parameters = null);

		/**
		 * @param $serviceKey
		 * @param $definition
		 * @return mixed
		 */
		public function setShared($serviceKey, $definition);

		/**
		 * @param $name
		 * @return DiInterface
		 */
		public function container($name);

		/**
		 * @param $serviceKey
		 * @return mixed
		 */
		public function getShared($serviceKey);

		/**
		 * @param $name
		 * @param DiInterface $di
		 * @return mixed
		 */
		public function setServiceContainer($name, DiInterface $di);

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
		 * @param $name
		 * @return $this
		 */
		public function removeService($name);

		/**
		 * @param $name
		 * @return $this
		 */
		public function removeContainer($name);

		/**
		 * @param $name
		 * @return mixed
		 */
		public function remove($name);

		/**
		 * @param $name
		 * @return ServiceInterface
		 */
		public function resetService($name);

		/**
		 * @param $name
		 * @return ServiceInterface
		 */
		public function getService($name);

	}
}

