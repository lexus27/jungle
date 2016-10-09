<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 17:31
 */
namespace Jungle\Util\Communication\Labaratory {

	/**
	 * Class Action
	 * @package Jungle\Util\Communication\Labaratory
	 */
	class Action implements ActionInterface{

		use SpecificationAwareTrait;

		/**
		 * @param array $params
		 * @param ProcessStackInterface $stack
		 */
		public function execute(array $params, ProcessStackInterface $stack){
			$process = new Process($this);
			$params = $this->prepareParams($params);
			$process->setParams($params);




			$this->specification->execute();




			$stack->addProcess($process);
		}


		/**
		 * @param array $params
		 */
		public function prepareParams(array $params = []){

		}



		/**
		 * @param $process
		 */
		public function onResponse($process){

		}

	}
}

