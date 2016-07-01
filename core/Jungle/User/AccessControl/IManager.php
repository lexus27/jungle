<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 18.02.2016
 * Time: 3:53
 */
namespace Jungle\User\AccessControl {

	/**
	 * Interface IManager
	 * @package Jungle\User\AccessControl
	 */
	interface IManager{

		/**
		 * @return mixed
		 */
		public function setContextAdapter();

		/**
		 * @return mixed
		 */
		public function getContextAdapter();

		/**
		 * Метод для вычисления изходя из текущих настроек контекста.
		 *
		 * @param $action
		 * @param $resource
		 * @return bool
		 */
		public function enforce($action, $resource);

		/**
		 * @param Context $context
		 * @return bool
		 */
		public function decise(Context $context);

	}
}

