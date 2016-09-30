<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.05.2016
 * Time: 20:27
 */
namespace Jungle\Util\Data\Condition {

	use Jungle\Util\Data\Schema\OuterInteraction\ValueAccessAwareInterface;

	/**
	 * Interface ConditionInterface
	 * @package Jungle\Util\Data\Condition
	 */
	interface ConditionInterface{

		/**
		 * @param \Jungle\Util\Data\Record\PropertyRegistryInterface|mixed $data
		 * @param null|ValueAccessAwareInterface|callable $access - if data is outer original data
		 * @return bool
		 */
		public function __invoke($data, $access = null);

		/**
		 * @return array
		 */
		public function toStorageCondition();

	}
}

