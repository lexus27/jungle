<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.09.2016
 * Time: 17:29
 */
namespace Jungle\Di\HolderChains {

	use Jungle\Di\DiInterface;


	/**
	 * Interface HolderManagerInterface
	 * @package Jungle\Di
	 */
	interface HolderManagerInterface{

		/**
		 * @param $alias
		 * @param $priority
		 * @return $this
		 */
		public function defineHolder($alias, $priority = 0.0);


		/**
		 * @param $holderAlias
		 * @return $this
		 */
		public function restoreInjection($holderAlias);

		/**
		 * @param $holderAlias
		 * @param DiInterface $di
		 * @return $this
		 */
		public function changeInjection($holderAlias, DiInterface $di);

		/**
		 * @param $holderAlias
		 * @return mixed
		 */
		public function getLastInjection($holderAlias);

		/**
		 * @param $holderAlias
		 * @return DiInterface|null
		 */
		public function getInjection($holderAlias);


	}
}

