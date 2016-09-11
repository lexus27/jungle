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

	use Jungle\Util\Data\Foundation\Registry\RegistryRemovableInterface;
	use Jungle\Util\Data\Foundation\Registry\RegistryWriteInterface;


	/**
	 * Interface DiSettingInterface
	 * @package Jungle\Di
	 */
	interface DiSettingInterface extends RegistryRemovableInterface, RegistryWriteInterface{

		/**
		 * @param $key
		 * @param $definition
		 * @param bool|false $shared
		 * @return mixed
		 */
		public function set($key, $definition, $shared = false);


		/**
		 * @param $serviceKey
		 * @param $definition
		 * @return mixed
		 */
		public function setShared($serviceKey, $definition);

		/**
		 * @param $name
		 * @param DiInterface $di
		 * @return mixed
		 */
		public function setServiceContainer($name, DiInterface $di);

		/**
		 * @param $name
		 * @return $this
		 */
		public function removeContainer($name);

		/**
		 * @param $name
		 * @return DiInterface
		 */
		public function container($name);




		/**
		 * @param $name
		 * @return mixed
		 */
		public function remove($name);


		/**
		 * @param $name
		 * @return $this
		 */
		public function removeService($name);

		/**
		 * @param $name
		 * @return ServiceInterface
		 */
		public function resetService($name);

	}
}

