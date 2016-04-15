<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 20:08
 */
namespace Jungle\Storage\Db\Lexer {

	/**
	 * Interface ISign
	 * @package Jungle\Storage\Db\Lexer
	 */
	interface ISign{

		/**
		 * @param SqlContext $context
		 * @param int $position
		 * @param null $dialect
		 * @param null $nextPoint
		 * @return mixed
		 */
		public function recognize(SqlContext $context, $position = 0, $dialect = null, & $nextPoint = null);



	}
}

