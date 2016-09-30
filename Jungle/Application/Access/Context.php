<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 29.09.2016
 * Time: 16:26
 */
namespace Jungle\Application\Access {
	
	use Jungle\Application\Dispatcher\ProcessInterface;
	use Jungle\Di\Injectable;
	use Jungle\User\AccessControl\ContextInterface;
	use Jungle\User\AccessControl\ContextTrait;
	use Jungle\User\AccessControl\Manager;
	use Jungle\User\UserInterface;

	/**
	 * Class Context
	 * @package Jungle\Application\Access
	 */
	class Context extends Injectable implements ContextInterface{

		use ContextTrait;

		/**
		 * @return UserInterface
		 */
		public function getUser(){
			return isset($this->properties['user'])?$this->properties['user']:$this->account->getUser();
		}

		/**
		 * @return mixed
		 */
		public function getProcess(){
			return isset($this->properties['process'])?$this->properties['process']:$this->process;
		}

		/**
		 * @param ProcessInterface $process
		 * @return $this
		 */
		public function setProcess(ProcessInterface $process){
			$this->properties['process'] = $process;
			return $this;
		}

	}
}

