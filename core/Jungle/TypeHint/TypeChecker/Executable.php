<?php
/**
 * Created by PhpStorm.
 * Project: jungle
 * Author: HoMe
 * Date: 14.11.2015
 * Time: 3:54
 */
namespace Jungle\TypeHint\TypeChecker{

    use Jungle\TypeHint\TypeChecker;

    /**
     * Class Executable (Callable alias)
     * @package Jungle\TypeHint\TypeChecker
     */
	class Executable extends TypeChecker{

        /** @var string  */
        protected $name = 'callable';

        /** @var array  */
        protected $aliases = ['callback','callable'];
	}
}

 