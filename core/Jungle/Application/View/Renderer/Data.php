<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.07.2016
 * Time: 2:55
 */
namespace Jungle\Application\View\Renderer {
	
	use Jungle\Application\Dispatcher\Controller\ProcessInterface;
	use Jungle\Application\View\Renderer;

	/**
	 * Class Data
	 * @package Jungle\Application\View
	 */
	abstract class Data extends Renderer{

		protected function _doInitialize(){}


		/**
		 * @param ProcessInterface $process
		 * @return mixed
		 */
		public function render(ProcessInterface $process){
			$string = $this->convert($this->extractData($process));
			return $string;
		}

		/**
		 * @param ProcessInterface $process
		 * @return mixed
		 */
		public function extractData(ProcessInterface $process){
			return $process->getResult();
		}

		/**
		 * @param $data
		 * @return string
		 */
		abstract public function convert($data);

	}
}

