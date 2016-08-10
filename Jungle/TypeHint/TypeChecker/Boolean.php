<?php
/**
 * Created by PhpStorm.
 * Project: jungle
 * Author: HoMe
 * Date: 14.11.2015
 * Time: 4:55
 */
namespace Jungle\TypeHint\TypeChecker{

	use Jungle\TypeHint\TypeChecker;

	/**
     * Class Boolean
     * @package Jungle\TypeHint\TypeChecker
     */
	class Boolean extends TypeChecker{

        /** @var string  */
        protected $name = 'boolean';

        /** @var array  */
        protected $aliases = ['bool','boolean'];

	}
}

 