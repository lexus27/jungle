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
	
	use Jungle\Application\Dispatcher\ProcessInterface;
	use Jungle\Application\View\Renderer;
	use Jungle\Application\ViewInterface;

	/**
	 * Class Data
	 * @package Jungle\Application\View
	 */
	abstract class Data extends Renderer{

		protected function _doInitialize(){}


		/**
		 * @param ProcessInterface $process
		 * @param ViewInterface $view
		 * @param array $variables
		 * @param array $options
		 * @return string
		 */
		public function render(ProcessInterface $process, ViewInterface $view, array $variables = [], array $options = []){
			return $this->convert($this->extractData($process));
		}

		/**
		 * @param \Jungle\Application\Dispatcher\ProcessInterface $process
		 * @return mixed
		 */
		public function extractData(ProcessInterface $process){
			$result = $process->getResult();
			if(!is_array($result)){
				return [
					'object' => $result
				];
			}else{
				return $result;
			}
		}

		/**
		 * @param $data
		 * @return string
		 */
		abstract public function convert($data);

	}
}

