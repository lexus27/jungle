<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.01.2016
 * Time: 13:08
 */
namespace Jungle\Communication\Stream\Specification {

	/**
	 * Class CodeRecognizer
	 * @package Jungle\Communication\Stream\Specification\CodeRecognizer
	 */
	class CodeRecognizer{

		/** @var  callable */
		protected $parser;

		/**
		 * @param callable $parser
		 * @return $this
		 */
		public function setParser(callable $parser){
			$this->parser = $parser;
			return $this;
		}

		/**
		 * @return callable
		 */
		public function getParser(){
			return $this->parser;
		}

		/**
		 * @param $data
		 * @return int|string
		 */
		public function recognize($data){
			if($this->validate($data)){
				return call_user_func($this->parser,$data);
			}
			return false;
		}

		/**
		 * @param $data
		 * @return bool
		 */
		public function validate($data){
			return (bool)$data;
		}

		/**
		 * @param $data
		 * @return int|string
		 */
		public function __invoke($data){
			return $this->recognize($data);
		}

	}
}

