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

		public function set($service_key, $service_definition);

		public function get($service_key);

		public function setShared($service_key,$service_definition);

		public function getShared($service_key);
		
	}
}

