<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 28.10.2015
 * Time: 0:40
 */
namespace Jungle\TypeHint\TypeChecker {

	use Jungle\TypeHint\TypeChecker;

	/**
     * Class String
     * @package Jungle\TypeHint\TypeChecker
     */
	class String extends TypeChecker{

        /** @var string  */
        protected $name = 'string';

        /** @var array  */
        protected $aliases = ['str','string'];

        /**
         * @param $value
         * @param array $parameters
         * @return bool
         */
        protected function validateByParameters($value, array $parameters = []){
            if(!is_string($value)){
                return false;
            }
            $p = $this->normalizeParameters($parameters,[
                'min'       => null,
                'max'       => null,
                'length'    => null,
                'not'       => null,
                'range'     => null,
                'match'     => null
            ]);

            if(is_numeric($p['min']) && (!isset($l) && ($l = strlen($value))!==null) && $l < $p['min']){
                $this->last_internal_error_message = 'String is less than "'.$p['min'].'"';
                return false;
            }

            if(is_numeric($p['max']) && (!isset($l) && ($l = strlen($value))!==null) && $l > $p['max']){
                $this->last_internal_error_message = 'String is long than "'.$p['max'].'"';
                return false;
            }

            if(is_numeric($p['length']) && (!isset($l) && ($l = strlen($value))!==null) && $l !== $p['length']){
                $this->last_internal_error_message = 'String is not compatible with length "'.$p['length'].'"';
                return false;
            }
            // do factory match
            if(is_callable($p['match']) && !$p['match']($value)){
                $this->last_internal_error_message = 'String is not compatible with matcher "'.$p['match'].'"';
                return false;
            }
            return true;

        }

	}
}

