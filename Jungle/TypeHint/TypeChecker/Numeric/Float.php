<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 28.10.2015
 * Time: 0:40
 */
namespace Jungle\TypeHint\TypeChecker\Numeric {

	use Jungle\TypeHint\TypeChecker;

	class Float extends TypeChecker\Numeric{

		/** @var string  */
		protected $name = 'float';

        /** @var array  */
        protected $aliases = ['double','float'];

	}
}

