<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.04.2016
 * Time: 21:20
 */
namespace Jungle\Application {

	/**
	 * Interface ResponseInterface
	 * @package Jungle\Application
	 */
	interface ResponseInterface{

		public function getFormatterType();

		public function getContent();

		public function send();

	}
}

