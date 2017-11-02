<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: ceive.data-attribute-context
 */

namespace App\Services\Router\Locator;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class CatchableException
 * @package Ceive\Data\AttributeContext
 */
class CatchableException extends \Exception{
	
	protected static $exception;
	
	public static function get(){
		if(!self::$exception){
			self::$exception = new CatchableException();
		}
		return self::$exception;
	}
}


