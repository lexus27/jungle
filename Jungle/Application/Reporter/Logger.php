<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.10.2016
 * Time: 19:01
 */
namespace Jungle\Application\Reporter {

	/**
	 * Class Logger
	 * @package Jungle\Application\Reporter
	 */
	class Logger{

		const LOG_INFO      = 'info';
		const LOG_NOTICE    = 'notice';
		const LOG_WARNING   = 'warning';
		const LOG_ERROR     = 'error';
		const LOG_CRASH     = 'crash';

		public function write($message, $type){

		}

	}
}

