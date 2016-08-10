<?php
/**
 * Created by PhpStorm.
 * Project: jungle
 * Author: HoMe
 * Date: 14.11.2015
 * Time: 3:53
 */
namespace Jungle\TypeHint\TypeChecker{

	use Jungle\TypeHint\TypeChecker;

	/**
     * Class Object
     * @package Jungle\TypeHint\TypeChecker
     */
    class Object extends TypeChecker{

        /** @var string  */
        protected $name     = 'object';

        /** @var array  */
        protected $alias    = ['obj','object','{}'];

	}
}

 