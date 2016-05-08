<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.04.2016
 * Time: 21:20
 */
namespace Jungle\HTTPFoundation {

	/**
	 * Interface ResponseInterface
	 * @package Jungle\HTTPFoundation
	 */
	interface ResponseInterface{

		public function getContent();

		public function getContentType();

		public function getStatusCode();

		public function getStatusText();


	}
}

