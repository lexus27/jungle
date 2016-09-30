<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 13:36
 */
namespace Jungle\Util\Data {
	
	use Jungle\Util\Data\Record\PropertyRegistryInterface;
	use Jungle\Util\Data\Record\PropertyRegistryRemovableInterface;
	use Jungle\Util\Data\Record\PropertyRegistryTransientInterface;

	/**
	 * Interface RecordInterface
	 * @package Jungle\Util\Data
	 */
	interface RecordInterface extends PropertyRegistryInterface, PropertyRegistryRemovableInterface, PropertyRegistryTransientInterface{}

}

