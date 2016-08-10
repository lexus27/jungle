<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 16.02.2016
 * Time: 19:03
 */
namespace Jungle\User\AccessControl\Policy\ConditionResolver\Exception {

	use Jungle\User\AccessControl\Policy\ConditionResolver\Exception;

	/**
	 * Class Query
	 * @package Jungle\User\AccessControl\Policy\ConditionResolver\Exception
	 */
	class Query extends Exception{

		/**
		 * @var string
		 */
		protected $path;

		/**
		 * @var string
		 */
		protected $error_path;

		protected $type = 'Query';

		public function getType(){
			return $this->type;
		}

		/**
		 * @param string $originalPath
		 * @param string $error_path
		 * @param bool $error_path_isFunction
		 * @param string $message
		 * @param int $code
		 * @param \Exception|null $previous
		 */
		public function __construct($originalPath, $error_path = null,$error_path_isFunction = false,$message = '',$code = 0,\Exception $previous = null){
			$this->path                     = $originalPath;
			$this->error_path               = $error_path;
			$this->error_path_function      = $error_path_isFunction;

			$o = $originalPath===$error_path?'paths['.$error_path.']':('OriginalPath['.$originalPath.']'.($error_path?' ErrorPath['.$error_path.']':''));
			$message = 'Query '.$this->getType().': '.$o. ($message?' Message: "'.$message.'"':'');
			parent::__construct($message,$code,$previous);
		}

		/**
		 * @return string
		 */
		public function getPath(){
			return $this->path;
		}

		/**
		 * @return mixed
		 */
		public function getErrorPath(){
			return $this->error_path;
		}

		/**
		 * @return bool
		 */
		public function isFunctionErrorPath(){
			return $this->error_path_function;
		}


	}
}

