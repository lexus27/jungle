<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.04.2016
 * Time: 23:52
 */
namespace Jungle\DependencyInjection {

	/**
	 * Interface DependencyInjectionInterface
	 * @package Jungle\DependencyInjection
	 */
	interface DependencyInjectionInterface{

		public function setShared($service_key,$service_definition);

		public function getShared($service_key);


		/**
		 * @param $service_key
		 * @param callable $definition
		 * @return mixed
		 */
		public function setBuilding($service_key, callable $definition);

		/**
		 * @param $serviceKey
		 * @param null $instanceKey
		 * @param ...$arguments
		 * @return mixed
		 */
		public function buildInstance($serviceKey, $instanceKey = null, ...$arguments);


		/**
		 * @param $serviceKey
		 * @param $instanceKey
		 * @return mixed
		 */
		public function getInstance($serviceKey, $instanceKey);

	}
}

