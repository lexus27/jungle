<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 22.11.2015
 * Time: 1:35
 */
class ThisAccessor{

	protected $name = 'NMAE';

	function getCallable(){
		return function(){
			$b = (new Backtrace());

			if(is_callable($this)){
				echo $this();
			}else{
				echo $this->name;
			}

			if(Backtrace::calledFrom('ThisAccessor','->{closure}')){// caller {App:Object}->test(510,110)
				echo 'App->test Вызвано из класса ThisAccessor, методом ->{closure}';
			}else{
				echo 'App->test Вызван из другого источника кроме ThisAccessor';
			}


			echo '<pre>';
			var_dump($b->getInfo());
			echo '</pre>';
			return 'INVOKED';
		};
	}
}
$g = new ThisAccessor();
$fn = $g->getCallable();
echo '<pre>';
$fn = $fn->bindTo($fn);
var_dump($fn);
echo '</pre>';


abstract class AApp{}
class App extends AApp {

	public function test($argument_001,$argument_002){
		self::staticTest($argument_001+10,$argument_002+10);
		global $fn;
		call_user_func($fn);


		if(Backtrace::calledFrom('Opa','::callApp')){// caller {App:Object}->test(510,110)
			echo 'App->test Вызвано из Opa::callApp';
		}else{
			echo 'App->test Вызван из другого источника кроме Opa::callApp';
		}

	}

	public static function staticTest($argument_001,$argument_002){

	}

	public static function s(){

/*
		while($caller){
			if($caller->isGlobalCall()){
				echo $caller->getFunctionName().'('.implode(', ',(array) $caller->getArgumentList()).')<br><br>';
			}else{
				echo $caller->getClassName().$caller->getCallType().$caller->_prefix().'('.implode(', ',(array) $caller->getArgumentList()).')<br><br>';
			}
			$caller = $caller->whoCaller();
		}
*/

	}

}
class Opa{

	public static function callApp(){
		$app = new App();
		$app->test(100,500);
	}

	public function hello(){
		echo 'ХЕЛЛООО БЛЯТЬ!';
	}

}
function myFunc(){
	Opa::callApp();
}
myFunc();

/**
 * Class Backtrace
 * @TODO Не все оказалось так просто, для работы с {closure} необходимо переработать класс Backtrace и возможно изменить структуру реализации
 */
class Backtrace{

	const TYPE_STATIC = '::';
	const TYPE_DYNAMIC = '->';


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
	 * @param null $trace
	 * @param null $prev
	 */
	public function __construct(& $trace = null,$prev = null){
		if($trace===null){
			$trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
			array_shift($trace);// remove Backtrace class item
		}
		if($prev) $this->prev = $prev;
		if(!empty($trace)){
			$this->info = array_shift($trace);
			if($trace){
				$this->next = new Backtrace($trace,$this);
			}
		}
	}

	/**
	 * @return bool
	 */
	public function isStaticCall(){
		return isset($this->info['type']) && $this->info['type']==='::';
	}

	/**
	 * @return bool
	 */
	public function isDynamicCall(){
		return isset($this->info['type']) && $this->info['type']==='->';
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

}