<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.09.2016
 * Time: 9:49
 */
namespace Jungle\Application\Notification {

	/**
	 * Class Complex
	 * @package Jungle\Application\Notification
	 */
	class Complex{


		/** @var  string */
		protected $represent_message = '';

		protected $represent_success = false;



		/** @var array  */
		protected $collections  = [];

		/** @var array  */
		protected $objects      = [];



		/** @var array  */
		protected $messages     = [];



		/** @var array  */
		protected $info         = [];

		/** @var array  */
		protected $notices      = [];

		/** @var array  */
		protected $warnings     = [];

		/** @var array  */
		protected $errors       = [];


		/**
		 * @param $modelAlias
		 */
		public function getCollection($modelAlias){

		}


		public function getObject($modelAlias){

		}


		/**
		 * @param $modelAlias
		 */
		public function getErrors($modelAlias){

		}

		/**
		 * @param $modelAlias
		 */
		public function getWarnings($modelAlias){

		}

		/**
		 * @param $modelAlias
		 */
		public function getInfo($modelAlias){

		}

		/**
		 * @param $modelAlias
		 */
		public function getMessages($modelAlias){

		}

	}
}

