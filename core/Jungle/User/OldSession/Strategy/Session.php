<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.08.2016
 * Time: 20:19
 */
namespace Jungle\User\OldSession\Strategy {
	
	use Jungle\User\OldSession\StrategyInterface;
	use Jungle\User\SessionInterface;

	/**
	 * Class Session
	 * @package Jungle\User\OldSession\StrategyInterface
	 */
	class Session implements StrategyInterface{
		
		/**
		 * @param $signature
		 */
		public function onNotFound($signature){




		}

		/**
		 * @param SessionInterface $session
		 */
		public function onOverdue(SessionInterface $session){
			// TODO: Implement catchOverdue() method.
		}

		/**
		 *
		 */
		public function onNotSupplied(){
			// TODO: Implement catchNotSupplied() method.
		}
	}
}

