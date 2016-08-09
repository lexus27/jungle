<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.08.2016
 * Time: 10:56
 */
namespace Jungle\User\OldSession {
	
	use Jungle\User\SessionInterface;

	/**
	 * Class StrategyInterface
	 * @package Jungle\User\OldSession
	 */
	interface StrategyInterface{

		/**
		 * @param $signature
		 */
		public function onNotFound($signature);

		/**
		 * @param SessionInterface $session
		 */
		public function onOverdue(SessionInterface $session);

		/**
		 *
		 */
		public function onNotSupplied();

	}
}

