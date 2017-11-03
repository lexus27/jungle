<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: ceive.data-attribute-context
 */

namespace Jungle\Application\Router;
use Jungle\Application\Router\Locator\CatchableException;
use Jungle\Application\Router\Locator\MissingException;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Locator
 * @package Ceive\Data\AttributeContext
 */
class Locator{
	
	/**
	 * todo: Пути - они же лексерные выражения, они же могут являться вызовами методов call() path
	 * todo: Substitution, and ObjectAccessor phantom object
	 * todo: AutocompleteInterface
	 * todo: Substritution or Custom LocationBehaviour
	 * @param $container
	 * @param $path
	 * @param array $meta
	 * @return mixed
	 */
	public function query($container, $path, array $meta = null ){
		
		if($meta===null){
			$path = $this->pathNormalize($path);
			$meta = [0, $container, $path];
		}else{
			$path = $this->pathDecomposite($path);
		}
		
		list($depth, $base_container, $start_path) = $meta;
		
		try{
			return $this->getFrom($container, $path);
		}catch(CatchableException $e){}
		
		//elapse increment
		$segment = array_shift($path);
		
		list($segment, $extra) = $this->segmentExtra($segment);
		
		if($segment){
			try{
				$value = $this->getFrom($container, $segment);
			}catch(CatchableException $e){
				return $this->onNotFound(
					$start_path, $base_container,
					$this->pathElapsed($start_path, null, $depth),$depth, $container, $segment, $path
				);
			}
		}else{
			$value = $container;
		}
		
		$value = $this->onFound(
			$start_path, $base_container,
			$this->pathElapsed($start_path, null, $depth),$depth, $container, $segment, $path, $value
		);
		
		if($extra){
			$value = $this->getByExtraFrom($value, $extra);
			$value = $this->onFoundExtra(
				$start_path, $base_container,
				$this->pathElapsed($start_path, null, $depth),$depth, $container, $segment, $path, $value,
				$extra, $value
			);
			
		}
		if($path){
			return $this->query($value, $path, [$depth+1, $base_container, $start_path] );
		}else{
			return $value;
		}
		
	}
	
	
	/**
	 * @param $container
	 * @param $key
	 * @return mixed
	 * @throws CatchableException
	 */
	public function getFrom($container, $key){
		$key = $this->pathString($key);
		return $this->_getFrom($container, $key);
	}
	
	/**
	 * @param $container
	 * @param $key
	 * @return mixed
	 * @throws CatchableException
	 */
	protected function _getFrom($container, $key){
		
		if(is_object($container)){
			
			if(property_exists($container,$key)){
				return $container->{$key};
			}
			
			if(method_exists($container,'__get') && isset($container->{$key})){
				return $container->{$key};
			}
			
			//todo my insertion
			$method = 'get'.$key;
			if(method_exists($container,$method)){
				return call_user_func([$container,$method]);
			}
			
			if($container instanceof \ArrayAccess && isset($container[$key])){
				return $container[$key];
			}
		}
		
		if(is_array($container)){
			if(array_key_exists($key, $container)){
				return $container[$key];
			}elseif(is_numeric($key)){
				$a = array_slice($container, intval($key), 1);
				if($a){
					return array_shift($a);
				}
			}
		}
		
		//notFound
		
		throw CatchableException::get();
	}
	
	/**
	 * @param $value
	 * @param $extra
	 * @return int|string
	 */
	public function getByExtraFrom($value, $extra){
		$extra = array_filter(explode(':', $extra));
		foreach($extra as $e){
			switch($e){
				case 'class':
				case 'type':
					$value = is_object($value)? get_class($value) : gettype($value);
					break;
				case 'count':
					$value = $value instanceof \Countable || is_array($value)? count($value) : 0;
					break;
					
				case 'bytes':
				case 'length':
					$value = is_string($value)? strlen($value) : 0;
					break;
					
				case 'length-mb':
				case 'length-utf':
				case 'length-multi-byte':
					$value = is_string($value)? mb_strlen($value) : 0;
					break;
					
				case 'rand':
					$value = rand();
					break;
					
					
					
				case 'first-item':
					$value = is_array($value) && $value? array_slice($value,0,1,false)[0] : null;
					break;
				case 'last-item':
					$value = is_array($value) && $value? array_slice($value,-1,1,false)[0] : null;
					break;
				
				case 'first-char':
					$value = is_scalar($value) && $value? mb_substr($value,0,1) : null;
					break;
				case 'last-char':
					$value = is_scalar($value) && $value? mb_substr($value,-1,1) : null;
					break;
					
				case 'first':
					if($value){
						if(is_array($value)) return array_slice($value,0,1,false)[0];
						else                 return mb_substr($value,0,1);
						
					}else $value = null;
					break;
				case 'last':
					if($value){
						if(is_array($value)) return array_slice($value,-1,1,false)[0];
						else                 return mb_substr($value,-1,1);
					}else $value = null;
					break;
			}
		}
		return $value;
		
	}
	
	/**
	 * @param array $basePath - [user, profile, fullname]
	 * @param mixed $baseContainer - c Context
	 *     -         -      -    -  -   -------------------
	 * @param array     $elapsedPath        - [user]        |
	 * @param int       $elapsedDepth       - 1             |
	 * @param mixed     $elapsedContainer   - c User        |----- Not Found
	 * @param string    $segment            - profile       |
	 *     -         -      -    -  -   -------------------
	 * @param array $rightPath - fullname
	 * @return mixed|null
	 * @throws MissingException
	 */
	public function onNotFound($basePath, $baseContainer, $elapsedPath, $elapsedDepth, $elapsedContainer, $segment, $rightPath){
		throw new MissingException($baseContainer, $basePath, $elapsedPath, $elapsedContainer, $segment);
	}
	
	/**
	 * @param $basePath
	 * @param $baseContainer
	 * @param $elapsedPath
	 * @param $elapsedDepth
	 * @param $elapsedContainer
	 * @param $segment
	 * @param $rightPath
	 * @param $value
	 * @return mixed
	 */
	public function onFound($basePath, $baseContainer, $elapsedPath, $elapsedDepth, $elapsedContainer, $segment, $rightPath, $value){
		return $value;
	}
	
	/**
	 * @param $basePath
	 * @param $baseContainer
	 * @param $elapsedPath
	 * @param $elapsedDepth
	 * @param $elapsedContainer
	 * @param $segment
	 * @param $rightPath
	 * @param $value
	 * @param $extra
	 * @param $extraValue
	 * @return mixed
	 */
	public function onFoundExtra($basePath, $baseContainer, $elapsedPath, $elapsedDepth, $elapsedContainer, $segment, $rightPath, $value, $extra, $extraValue){
		return $extraValue;
	}
	
	
	
	
	public function pathString($path){
		return (is_array($path)?implode('.',$path):$path);
	}
	
	/***
	 * @param $path
	 * @return array
	 */
	public function pathDecomposite($path){
		if(!is_array($path)){
			$path = array_diff(explode('.',$path),[null,'']);
		}
		return $path;
	}
	
	/**
	 * @param $path
	 * @return array
	 */
	public function pathNormalize($path){
		if(!is_array($path)){
			$path = array_diff(explode('.',$path),[null,'']);
		}else{
			$a = [];
			foreach($path as $c){
				if(is_array($c)){
					$a = array_merge($a, $c);
				}elseif(is_string($c) && strpos($c,'.')!==false){
					$a = array_merge($a, array_diff(explode('.',$path),[null,'']));
				}
			}
			$path = $a;
		}
		return $path;
	}
	
	public function segmentExtra($segment){
		if(strpos($segment, ':')!==false){
			$segment = array_replace([null,null],explode( ':' , $segment,2));
			foreach($segment as &$a){
				if(empty($a))$a = null;
			}
			return $segment;
		}
		return [$segment,null];
	}
	
	/**
	 * @param $start_path
	 * @param $ahead_path
	 * @param null $depth
	 * @return array
	 */
	public function pathElapsed($start_path, $ahead_path = null, $depth = null){
		
		if(!is_null($depth)){
			$start_path = $this->pathDecomposite($start_path);
			return array_slice($start_path, 0 , $depth);
		}
		
		if(!is_null($ahead_path)){
			$ahead_path = $this->pathDecomposite($ahead_path);
			$start_path = $this->pathDecomposite($start_path);
			return array_diff($start_path, $ahead_path);
		}
		
		return null;
	}
	
	/**
	 * @return Locator
	 */
	public static function get(){
		static $locator;
		if(!$locator){
			$locator = new Locator();
		}
		return $locator;
	}
	
}


