<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 16:31
 */
namespace Jungle\Util\Communication\Labaratory {

	/**
	 * Interface ProcessInterface
	 * @package Jungle\Util\Communication\Labaratory
	 */
	interface ProcessInterface{



		/**
		 * @return ActionInterface
		 */
		public function getAction();

		/**
		 * @param $result
		 * @return $this
		 */
		public function setResult($result);

		/**
		 * @return mixed
		 */
		public function getResult();


		/**
		 * @param bool|true $wait
		 * @return $this
		 */
		public function setWaiting($wait = true);

		/**
		 * @return bool
		 */
		public function isWaiting();


		/**
		 * @param bool|true $canceled
		 * @return $this
		 */
		public function setCanceled($canceled = true);

		/**
		 * @return bool
		 */
		public function isCanceled();




		/**
		 * @param array $params
		 * @return $this
		 */
		public function setParams(array $params);


		/**
		 * @param $key
		 * @return mixed
		 */
		public function __get($key);


		/**
		 * @param $key
		 * @return mixed
		 */
		public function __isset($key);


	}
}

