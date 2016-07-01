<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 6:41
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections {

	/**
	 * Interface INamedAware
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections
	 */
	interface INamedOwner{

		/**
		 * @see IRegistryRead::get alias in IRegistry
		 * @param $key
		 * @return \Jungle\Util\INamed
		 */
		public function getByKey($key);

	}
}

