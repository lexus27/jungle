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
	
	use Jungle\Application\Notification\Responsible;
	use Jungle\User\OldSession\StrategyInterface;
	use Jungle\User\SessionInterface;

	/**
	 * Class Token
	 * @package Jungle\User\OldSession\StrategyInterface
	 */
	class Token implements StrategyInterface{
		
		/**
		 * @param $signature
		 * @throws Responsible
		 */
		public function onNotFound($signature){
			throw new Responsible('Unexpected token');
		}

		/**
		 * @param SessionInterface $session
		 * @throws Responsible
		 */
		public function onOverdue(SessionInterface $session){
			throw new Responsible('Overdue token');
		}

		/**
		 *
		 */
		public function onNotSupplied(){
			throw new Responsible('Not Authorized');
		}
	}
}

