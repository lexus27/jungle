<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.08.2016
 * Time: 0:02
 */
namespace Jungle\Application\Dispatcher\Exception {
	
	use Jungle\Application\Dispatcher\Exception;
	use Jungle\Application\Dispatcher\ProcessInterface;

	/**
	 * Class Forwarded
	 * @package Jungle\Application\Dispatcher\Exception
	 */
	class Forwarded extends Exception{

		/** @var  ProcessInterface */
		protected $process;

		/**
		 * Forwarded constructor.
		 * @param ProcessInterface|string $process
		 */
		public function __construct(ProcessInterface $process){
			$this->process = $process;
			parent::__construct('Forwarded');
		}

		/**
		 * @return ProcessInterface
		 */
		public function getProcess(){
			return $this->process;
		}

	}
}

