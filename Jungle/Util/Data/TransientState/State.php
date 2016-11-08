<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.10.2016
 * Time: 11:05
 */
namespace Jungle\Util\Data\TransientState {

	/**
	 * Class State
	 * @package Jungle\Util\Data\TransientState
	 */
	class State{

		/** @var array  */
		protected $data;

		/**
		 * State constructor.
		 * @param array $data
		 * @param State $prev
		 */
		public function __construct(array $data, State $prev = null){
			$this->data = $data;
			$this->prev = $prev;
		}

		/**
		 * @return array
		 */
		public function data(){
			return $this->data;
		}

		/**
		 * @return State
		 */
		public function prev(){
			return $this->prev;
		}
	}
}

