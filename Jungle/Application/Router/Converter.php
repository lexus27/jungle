<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: c4l-rebuild.local
 */

namespace App\Services\Router;


use Jungle\Http\Request;

class Converter{
	
	public $map = [];
	
	/**
	 * @var string
	 */
	private $activateMethod;
	
	public function __construct(array $map = [], $activateMethod = null){
		$this->map = $map;
		$this->activateMethod = $activateMethod;
	}
	
	public function __invoke($p, Request $request){
		if(!$this->activateMethod || (
				is_string($this->activateMethod) && (
					strtolower($request->getMethod()) === strtolower($this->activateMethod)
				)
			) || (
				is_array($this->activateMethod) && in_array(strtolower($request->getMethod()),$this->activateMethod)
			)
		){
			foreach($this->map as $key => $value){
				if(is_int($key)){
					if($this->activateMethod){
						$p[$value] = $this->_resolve($request, '{'.$this->activateMethod.'.'.$value.'}');
					}else{
						$p[$value] = $this->_resolve($request, '{'.$value.'}');
					}
					
				}else{
					$p[$key] = $this->_resolve($request, $value);
				}
			}
		}
		return $p;
	}
	
	
	public function _resolve(Request $request, $value){
		
		if($value instanceof Binding){
			$a = $request->getParam();
			return $value->composite($a);
		}
		
		if(is_string($value)){
			if(substr($value,0,1) === '{' && substr($value,-1) === '}'){
				$key = substr($value,1,-1);
				$locator = Locator::get();
				return $locator->query($request, $key);
			}
		}elseif(is_callable($value)){
			return call_user_func($value, $request);
		}
		return $value;
	}
	
	public static function create(array $map = [], $activateMethod = null){
		return new Converter($map, $activateMethod);
	}
}


