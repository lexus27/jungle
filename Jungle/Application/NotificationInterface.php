<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 21.07.2016
 * Time: 0:09
 */
namespace Jungle\Application {

	/**
	 * Interface NotificationInterface
	 * @package Jungle\Application
	 *
	 * Success | Info | Warning | Error | Internal Error
	 *
	 * Ошибка валидации поля объекта.
	 * Ошибка доступа
	 * Ошибка существования объекта по адресу
	 *
	 * Оповещении о состоянии SUCCESS | INFO | FAILURE
	 *
	 */
	interface NotificationInterface{

		/**
		 * @return bool
		 */
		public function isSuccess();

		/**
		 * @return bool
		 */
		public function isInfo();

		/**
		 * @return bool
		 */
		public function isWarning();

		/**
		 * @return bool
		 */
		public function isError();

		public function hasMessage();

		public function getMessage();

		/**
		 * @param $schema_name
		 * @return mixed
		 */
		public function hasObject($schema_name);

		/**
		 * @param $schema_name
		 * @return mixed
		 */
		public function getObject($schema_name);


		/**
		 * @param $schema_name
		 * @return mixed
		 */
		public function hasCollection($schema_name);

		/**
		 * @param $schema_name
		 * @return mixed
		 */
		public function getCollection($schema_name);


		/**
		 * @param $schema_name
		 * @return mixed
		 */
		public function hasValidationErrors($schema_name);

		/**
		 * @param $schema_name
		 * @return mixed
		 */
		public function getValidationErrors($schema_name);

	}
}

