<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.07.2016
 * Time: 14:25
 */
namespace Jungle\Util\Communication\HttpFoundation {

	/**
	 * Interface BrowserInterface
	 * @package Jungle\Util\Communication\HttpFoundation
	 */
	interface BrowserInterface{

		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @return string
		 */
		public function getVersion();

		/**
		 * @return bool
		 */
		public function isMobile();

		/**
		 * @return bool
		 */
		public function isUnknown();

		/**
		 * @return string
		 */
		public function getPlatform();


		/**
		 * @return string
		 */
		public function getUserAgent();




		/**
		 * @return string|null
		 */
		public function getBestLanguage();

		/**
		 * @return string[]
		 */
		public function getDesiredLanguages();

		/**
		 * @return string|null
		 */
		public function getBestCharset();

		/**
		 * @return array
		 */
		public function getDesiredCharsets();

		/**
		 * Методы на медиа-тип целесообразны в контексте запроса нежели глобально.
		 * В случае отправки запроса для формирования HTML страницы. клиент использует text/html в Accept
		 * В случае отправки запроса для получения изображений или других медиа,
		 * инициаторами обычно выступают HTML Теги, src="" и так далее,
		 * в таких случаях заголовок Accept подставляется уже по семантике самого тега.
		 * Тоесть какой медиатип принять решает инициатор посредством запроса.
		 *
		 * Попробуем установить какуюто сущность, в контексте которой будет инициироваться запрос.
		 *
		 * $this->getDispatcher('images')->get('url...', params...);
		 *
		 * КонтентМенеджер определяет ряд соответствущих ему медиа типов.
		 *
		 * $result = $this->getContentsManager('images')->get('url...', params...);
		 * $result is ContentInterface with response media type
		 *
		 *
		 * @return string|null
		 */
		public function getBestMediaType();

		/**
		 * @return array
		 */
		public function getDesiredMediaTypes();






	}
}

