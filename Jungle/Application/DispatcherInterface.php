<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 14.07.2016
 * Time: 23:06
 */
namespace Jungle\Application {
	
	use Jungle\Application\Dispatcher\Process\ProcessInitiatorInterface;

	/**
	 * Interface DispatcherInterface
	 * @package Jungle\Application
	 */
	interface DispatcherInterface{

		/**
		 * @param RequestInterface $request
		 * @return ResponseInterface
		 */
		public function dispatch(RequestInterface $request);

		/**
		 * @param $reference
		 * @param null $data
		 * @param null $options
		 * @param ProcessInitiatorInterface|null $initiator
		 * @return mixed
		 */
		public function control($reference, $data = null, $options = null, ProcessInitiatorInterface $initiator = null);

		/**
		 * @param $alias
		 * @param StrategyInterface $strategy
		 * @return $this
		 */
		public function setStrategy($alias, StrategyInterface $strategy);

		/**
		 * @param $alias
		 * @return StrategyInterface|null
		 */
		public function findStrategy($alias);


		/**
		 * @param RequestInterface $request
		 * @return StrategyInterface|null
		 */
		public function matchStrategyBy(RequestInterface $request);
	}
}

