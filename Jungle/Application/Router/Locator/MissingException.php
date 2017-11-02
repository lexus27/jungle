<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: ceive.data-attribute-context
 */

namespace App\Services\Router\Locator;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class MissingException
 * @package App\Services\Router\Locator
 */
class MissingException extends Exception{
	
	public $root;
	
	public $called_path;
	
	public $container;
	
	public $container_path;
	
	public $container_missing_key;
	
	/**
	 * MissingException constructor.
	 * @param string $root
	 * @param string $calledPath
	 * @param \Exception $containerPath
	 * @param int $container
	 * @param $containerMissingKey
	 *
	 *
	 * container.{missingKey} is missing
	 */
	public function __construct($root, $calledPath, $containerPath, $container, $containerMissingKey){
		$this->root                     = $root;
		$this->called_path              = $calledPath;
		$this->container                = $container;
		$this->container_path           = $containerPath;
		$this->container_missing_key    = $containerMissingKey;
		parent::__construct("Not found: {root}.{$calledPath}, missing in {$containerPath}.{$containerMissingKey}");
	}
	
}


