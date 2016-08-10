<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 28.10.2015
 * Time: 0:40
 */
namespace Jungle\TypeHint\TypeChecker\Numeric {

	use Jungle\TypeHint\TypeChecker;

	/**
     * Class Integer
     * @package Jungle\TypeHint\TypeChecker\Numeric
     */
	class Integer extends TypeChecker\Numeric{

		/** @var string  */
		protected $name = 'integer';

        /** @var array  */
        protected $aliases = ['int','integer'];

	}
}

