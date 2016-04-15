<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 20.11.2015
 * Time: 19:41
 */
namespace Jungle {

	/**
	 * Class Backtrace
	 * @package Jungle
	 *
	 */
	class Backtrace{

		const TYPE_STATIC   = '::';
		const TYPE_DYNAMIC  = '->';

		/**
		 * @var array
		 */
		protected static $files = [];

		/**
		 * @var int
		 */
		protected static $max_files = 10;

		/**
		 * @var Backtrace|null
		 */
		protected $next;

		/**
		 * @var Backtrace|null
		 */
		protected $prev;

		/**
		 * @var array
		 */
		protected $info;


		/**
		 * @return Backtrace|null
		 */
		public function next(){
			return $this->next;
		}

		/**
		 * @return Backtrace|null
		 */
		public function whoCaller(){
			return $this->next;
		}

		/**
		 * @return Backtrace|null
		 */
		public function prev(){
			return $this->prev;
		}

		/**
		 * @param array|null $trace
		 * @param Backtrace|null $prev
		 */
		public function __construct(array & $trace = null,Backtrace $prev = null){
			if($trace===null){
				$trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
				array_shift($trace);// remove Backtrace class item
			}
			if($prev){
				$this->prev = $prev;
			}
			if(!empty($trace)){
				$this->info = array_shift($trace);
				if($trace){
					$this->next = new Backtrace($trace,$this);
				}
			}
		}

		/**
		 * @return array
		 */
		public static function func_get_args(){
			$d = debug_backtrace(0,2);
			return $d[1]['args'];
		}

		/**
		 * @return bool
		 */
		public function isStaticCall(){
			return isset($this->info['type']) && $this->info['type']===self::TYPE_STATIC;
		}

		/**
		 * @return bool
		 */
		public function isDynamicCall(){
			return isset($this->info['type']) && $this->info['type']===self::TYPE_DYNAMIC;
		}

		/**
		 * @return bool
		 */
		public function isGlobalCall(){
			return !isset($this->info['type']);
		}

		/**
		 * @return bool
		 */
		public function hasObject(){
			return isset($this->info['object']);
		}

		/**
		 * @return null|object
		 */
		public function provideObject(){
			return isset($this->info['object'])?$this->info['object']:null;
		}

		public function getCallType(){
			return isset($this->info['type'])?$this->info['type']:null;
		}

		/**
		 * @return null|string
		 */
		public function getClassName(){
			return !$this->isGlobalCall()?$this->info['class']:null;
		}

		/**
		 * @return string
		 */
		public function getMethodName(){
			return $this->info['function'];
		}

		/**
		 * @return string
		 */
		public function getFunctionName(){
			return $this->info['function'];
		}

		/**
		 * @return string
		 */
		public function getArgumentList(){
			return $this->info['args'];
		}

		/**
		 * @param $className
		 * @return bool
		 */
		public function classIsDerivedFrom($className){
			if($this->isGlobalCall()){
				return $className?null:true;
			}
			$class = $this->info['class'];
			return is_a($class,$className,true);
		}

		/**
		 * @return null|object
		 */
		public function getObject(){
			return $this->isDynamicCall()?$this->info['object']:null;
		}

		/**
		 * @return callable
		 */
		public function getCallable(){
			if($this->isGlobalCall()){
				return $this->getFunctionName();
			}elseif($this->isDynamicCall()){
				return [$this->getObject(),$this->getMethodName()];
			}else{
				return [$this->getClassName(),$this->getMethodName()];
			}
		}

		/**
		 * @return array|mixed
		 */
		public function getInfo(){
			return $this->info;
		}

		/**
		 * @return mixed
		 */
		public function call(){
			return call_user_func_array($this->getCallable(),func_get_args());
		}

		/**
		 * @param $class_name
		 * @param null|true|false|string $method
		 * if true - check _call type to dynamic
		 * if false - check _call type to static
		 * if null - normal simple check derived class
		 * if string - check caller function equal case method string
		 * if string start with :: or -> define addition check calling type
		 * @param int $upLevel
		 * @return bool
		 */
		public static function calledFrom($class_name, $method = null, $upLevel = 0){
			$trace = debug_backtrace(0, 3 + $upLevel);
			$info = array_pop($trace);
			return
				isset($info['class']) &&
				is_a($info['class'], $class_name, true) && (
					$method === null ||
					(in_array($method,[true,self::TYPE_DYNAMIC],true) && $info['type'] === self::TYPE_DYNAMIC) ||
					(in_array($method,[false,self::TYPE_STATIC],true) && $info['type'] === self::TYPE_STATIC) ||
					(
						is_string($method) && (
						(
							($before = substr($method,0,2)) && (
								(
									in_array($before,[self::TYPE_STATIC,self::TYPE_DYNAMIC],true) &&
									$info['type'] === $before &&
									($method = substr($method,2))
								) || !in_array($before,[self::TYPE_STATIC,self::TYPE_DYNAMIC],true)
							)
						)
						) && strcasecmp($info['function'], $method) === 0

					)
				);
		}

		/**
		 * @return \SplFileInfo
		 */
		public function getFileInfo(){
			$file_path = $this->info['file'];
			if(!isset(self::$files[$file_path]) && is_file($file_path)){
				self::$files[$file_path] = new \SplFileInfo($file_path);
				if(count(self::$files) > self::$max_files){
					array_shift(self::$files);
				}
			}
			return self::$files[$file_path];
		}

		/**
		 *
		 */
		public function getCodeWrap(){
			$info = $this->getFileInfo();
			$file = $info->openFile('r');

			$this->getLine();



			foreach($file as $i => $line){



			}
		}

		/**
		 * @return integer
		 */
		public function getLine(){
			return $this->info['line'];
		}
	}
}

