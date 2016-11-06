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
			return $this->convert($this->extractData($process,$variables));
		}

		/**
		 * @param \Jungle\Application\Dispatcher\ProcessInterface $process
		 * @param array $variables
		 * @return mixed
		 */
		public function extractData(ProcessInterface $process,array $variables = []){
			$o = [
				'success'   => $process->getState()===$process::STATE_SUCCESS,
				'tasks'     => [],
				'result'    => $process->getResult(),
			];
			if($process->hasTasks()){
				foreach($process->getTasks() as $key => $task){
					if(is_object($task)){
						if($task instanceof \Exception){
							$o['tasks'][$key] = $task->getMessage();
						}else{
							$o['tasks'][$key] = true;
						}
					}else{
						$o['tasks'][$key] = (string)$task;
					}

				}
			}



			/*
			if(!is_array($result)){
				$o['object'] = $result;
			}else{
				$o = $result;
			}
			*/
			if(isset($variables['global_data']) && is_array($variables['global_data'])){
				return array_replace($variables['global_data'], $o);
			}
			return $o;
		}

		/**
		 * @param $data
		 * @return string
		 */
		abstract public function convert($data);

	}
}

