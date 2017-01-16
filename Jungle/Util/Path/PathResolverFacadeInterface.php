<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.12.2016
 * Time: 20:20
 */
namespace Jungle\Util\Path {

	/**
	 * Interface PathResolverFacadeInterface
	 * @package Jungle\Util\Path
	 */
	interface PathResolverFacadeInterface{

		/**
		 * @param $container
		 * @param $path
		 * @param null $container_path
		 */
		public function resolve($container, $path, $container_path = null);

	}
}

