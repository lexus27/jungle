<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: c4l-rebuild.local
 */

namespace Jungle\Application\Router;


use Jungle\Application\Dispatcher\Exception\ContinueRoute;
use Jungle\Data\Record\Model;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Binding
 * @package App\Services\Router
 *
 * new Binding(
 *    function(User $value){      return ['user_id' => $value->id];            },
 *    function(array $params){    return User::findFirst($params['user_id']);  },
 *    ['user_id']
 *)
 *
 */
class Bind implements BindingInterface{
	
	protected $paramKey;
	protected $classname;
	protected $objectPropertyName;//defaults "id"
	protected $required = true;
	
	public function __construct($paramKey, $classname, $objectPropertyName = null){
		
		
		$this->paramKey = $paramKey;
		$this->classname = $classname;
		$this->objectPropertyName = $objectPropertyName?:'id';
	}
	
	/**
	 * @param array $params
	 * @return mixed - value, prepare after match
	 * @throws \Exception
	 */
	public function composite(array $params){
		if(!is_a($this->classname, Model::class, true)){
			throw new \Exception('Could not be found "'.$this->classname.'" model, because is not a "'.Model::class.'" instance, use this derivative');
		}
		/** @var Model $a */
		$a = $this->classname;
		
		if(!array_key_exists($this->paramKey, $params)){
			if($this->required){
				
			}
			return null;//todo allow null
		}
		
		$result = $a::findFirst([
			$this->objectPropertyName => $params[$this->paramKey]
		]);
		
		
		if(!$result && $this->required){
			throw new ContinueRoute();
		}
		return $result;
	}
	
	/**
	 * @param array $params
	 * @return array - complete parameters after composite, interceptor for remove params carrier
	 */
	public function afterComposite(array $params){
		unset($params[$this->paramKey]);
		return $params;
	}
	
	/**
	 * @param $value
	 * @return array - value to params needle for link generation
	 * @throws \Exception
	 */
	public function decomposite($value){
		if(!is_a($value,$this->classname)){
			throw new \Exception('Decomposite could not be run. Passed instance a not is "'.$this->classname.'" class');
		}
		return [$this->paramKey => $value->{$this->objectPropertyName}];
		
	}
	
	/**
	 * @param $paramKey
	 * @param $classname
	 * @param null $objectPropertyName
	 * @return Bind
	 */
	public static function create($paramKey, $classname, $objectPropertyName = null){
		return new self($paramKey, $classname, $objectPropertyName);
	}
}


