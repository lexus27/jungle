<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.04.2016
 * Time: 21:18
 */
namespace Jungle\Application\Dispatcher {

	use Jungle\Application\DispatcherInterface;


	/**
	 * Interface ControllerInterface
	 * @package Jungle\Application
	 */
	interface ControllerInterface{

		/**
		 * @return void
		 */
		public function initialize();

		/**
		 * @param DispatcherInterface $dispatcher
		 * @param ModuleInterface $module
		 * @param $params
		 * @param $reference
		 * @param $initiator
		 * @return ProcessInterface
		 */
		public function factoryProcess(DispatcherInterface $dispatcher, ModuleInterface $module, $params, $reference, $initiator);

		/**
		 * @return array
		 */
		public function getDefaultMetadata();

	}
}

