<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 29.09.2016
 * Time: 9:51
 */
namespace Jungle\User\AccessControl\Matchable\Resolver {
	
	use Jungle\User\AccessControl\Context;
	use Jungle\User\AccessControl\ContextInterface;
	use Jungle\User\AccessControl\Matchable\Result;

	/**
	 * Class Inspector
	 * @package Jungle\User\AccessControl\Matchable\Resolver
	 */
	abstract class Inspector{

		/** @var  Context */
		protected $context;

		/** @var  Result */
		protected $result;

		/** @var  mixed */
		protected $expression;

		/** @var  array */
		protected $parsed;

		/** @var  array */
		protected $processed;

		/** @var  string */
		protected $process_key;

		/** @var  mixed */
		protected $mode;

		/**
		 * @param $mode
		 * @return $this
		 */
		public function setMode($mode = null){
			$this->mode = $mode;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getMode(){
			return $this->mode;
		}

		/**
		 * @param ContextInterface $context
		 * @param Result $result
		 * @param $expression
		 * @return $this
		 */
		public function beginInspect(ContextInterface $context, Result $result, $expression){
			$this->context = $context;
			$this->result = $result;
			$this->expression = $expression;
			return $this;
		}

		/**
		 * @param $expression
		 * @return $this
		 */
		public function expressionParsed(array $expression){
			$this->parsed = $expression;
			return $this;
		}

		/**
		 * @param $processed
		 * @return $this
		 */
		public function expressionProcessed(array $processed){
			$this->processed = $processed;
			return $this;
		}
		

		/**
		 * @param $result
		 * @return mixed
		 */
		public function checkoutResult($result){
			return $result;
		}

		/**
		 * @param $chunk
		 * @param $depth
		 * @param $container
		 * @param $path
		 * @param $fullPath
		 * @param $definition
		 * @return
		 */
		abstract public function onNotFound($chunk, $depth, $container, $path, $fullPath, $definition);

		/**
		 * @param $value
		 * @param $chunk
		 * @param $depth
		 * @param $container
		 * @param $path
		 * @param $fullPath
		 * @param $definition
		 * @return
		 */
		public function onFound($value, $chunk, $depth, $container, $path, $fullPath, $definition){
			return $value;
		}



		/**
		 * @param $string
		 * @return $this
		 */
		public function beforeProcess($string){
			$this->process_key = $string;
			return $this;
		}

	}
}

