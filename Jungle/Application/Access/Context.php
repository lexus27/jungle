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
	use Jungle\Di\InjectionAwareInterface;
	use Jungle\Di\InjectionAwareTrait;
	use Jungle\User\AccessControl\Context\ContextInterface;
	use Jungle\User\UserInterface;

	/**
	 * Class Context
	 * @package Jungle\Application\Access
	 */
	class Context extends \Jungle\User\AccessControl\Context\Context implements InjectionAwareInterface, ContextInterface{

		use InjectionAwareTrait;

		/**
		 * @return UserInterface
		 */
		public function getUser(){
			return isset($this->properties['user'])?$this->properties['user']:$this->getDi()->account->getUser();
		}

		/**
		 * @return mixed
		 */
		public function getProcess(){
			return isset($this->properties['process'])?$this->properties['process']:$this->getDi()->process;
		}

		/**
		 * @param $name
		 * @return mixed|null
		 */
		public function __get($name){
			switch($name){
				case 'process': return $this->getProcess();
			}
			return parent::__get($name);
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function __isset($name){
			switch($name){
				case 'process': return !!$this->getProcess();
			}
			return parent::__isset($name);
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

