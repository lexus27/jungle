<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 16.02.2016
 * Time: 19:05
 */
namespace Jungle\User\AccessControl\Matchable\ConditionResolver\Exception {

	/**
	 * Class InvalidQuery
	 * @package Jungle\User\AccessControl\Matchable\ConditionResolver\Exception
	 */
	class InvalidQuery extends Query{
		protected $type = 'invalid_definition';

	}
}

