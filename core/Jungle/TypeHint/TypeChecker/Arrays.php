<?php
/**
 * Created by PhpStorm.
 * Project: jungle
 * Author: HoMe
 * Date: 14.11.2015
 * Time: 4:34
 */
namespace Jungle\TypeHint\TypeChecker{

    use Jungle\TypeHint;
    use Jungle\TypeHint\TypeChecker;

    /**
     * Class Arrays
     * @package Jungle\TypeHint\TypeChecker
     */
	class Arrays extends TypeChecker{

        /** @var string  */
        protected $name = 'array';

        /** @var array  */
        protected $aliases = ['array','array-assoc','array-numeric','[]','[s]','[i]'];

        /**
         * @param mixed $value
         * @param array $parameters
         * @return bool
         */
        public function check($value, array $parameters = []){
            if(!is_array($value) && (!$value instanceof \ArrayAccess || !$value instanceof \Countable || !$value instanceof \Traversable)){
                return false;
            }

            if($this->validateByParameters($value, $parameters)===false){
                return false;
            }



            $parameters = $this->normalizeParameters($parameters,[
                'min'           => null,
                'max'           => null,
            ]);

            $verified   = $parameters['verified'];

            if(!in_array($verified,['array-assoc','[s]','array-numeric','[i]','array','[]'],true)){
                throw new \LogicException('Ha Ha ');
            }

            if(is_numeric($parameters['min']) && (!isset($count) && ($count = count($value))!==null) && $count < $parameters['min'] ){
                $this->last_internal_error_message = 'Array is less than '.$parameters['min'].'';
                return false;
            }
            if(is_numeric($parameters['max']) && (!isset($count) && ($count = count($value))!==null) && $count > $parameters['max'] ){
                $this->last_internal_error_message = 'Array is long than '.$parameters['max'].'';
                return false;
            }

            if(in_array($verified,['array-assoc','[s]'])){
                foreach ($value as $key => & $v) {
                    if(!is_string($key)){
                        $this->last_internal_error_message = 'Array is not full associative indexes';
                        return false;
                    }
                }
            }elseif(in_array($verified,['array-numeric','[i]'])){
                foreach ($value as $key => & $v) {
                    if(!is_numeric($key)){
                        $this->last_internal_error_message = 'Array is not full numeric indexes';
                        return false;
                    }
                }
            }

            return true;
        }

	}
}

 