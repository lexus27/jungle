<?php
/**
 * Created by PhpStorm.
 * Project: jungle
 * Author: HoMe
 * Date: 14.11.2015
 * Time: 4:44
 */
namespace Jungle\TypeHint\TypeChecker{

    use Jungle\TypeHint;
    use Jungle\TypeHint\TypeChecker;

    /**
     * Class Any
     * @package Jungle\TypeHint\TypeChecker
     */
    class Any extends TypeChecker{

        /** @var string  */
        protected $name = 'any';

        /**
         * @param mixed $value
         * @return bool
         */
        public function check($value){
            return true;
        }

        /**
         * @param string $type
         *
         * @return bool
         */
        public function verify($type){
            return empty($type);
        }

	}
}

 