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
		 * @param StrategyInterface|string $strategy
		 * @param float $priority
		 * @return $this
		 */
		public function setStrategy($alias, $strategy, $priority = 0.0);

		/**
		 * @param RequestInterface $request
		 * @return StrategyInterface|null
		 */
		public function matchStrategy(RequestInterface $request);
	}
}

