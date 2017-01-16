<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.11.2016
 * Time: 22:07
 */
namespace Jungle\Data\Record {

	/**
	 * Class Snapshot
	 * @package Jungle\Data\Record
	 */
	class Snapshot{

		/** @var array  */
		protected $data;

		/**
		 * State constructor.
		 * @param array $data
		 * @param Snapshot $prev
		 */
		public function __construct(array $data, Snapshot $prev = null){
			$this->data = $data;
			$this->prev = $prev;
		}

		/**
		 * @param array $data
		 */
		public function setData(array $data){
			$this->data = $data;
		}

		/**
		 * @return array
		 */
		public function data(){
			return $this->data;
		}

		/**
		 * @return Snapshot
		 */
		public function prev(){
			return $this->prev;
		}

		/**
		 * @return Snapshot
		 */
		public function earliest(){
			return !$this->prev?$this:$this->prev;
		}

		public function get($key, $default = null){
			return isset($this->data[$key])?$this->data[$key]:$default;
		}

		public function set($key, $value){
			$this->data[$key] = $value;
			return $this;
		}

	}
}

