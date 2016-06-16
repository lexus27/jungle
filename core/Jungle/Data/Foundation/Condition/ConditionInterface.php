<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.05.2016
 * Time: 20:27
 */
namespace Jungle\Data\Foundation\Condition {

	use Jungle\Data\Foundation\Schema\OuterInteraction\ValueAccessAwareInterface;

	/**
	 * Interface ConditionInterface
	 * @package Jungle\Data\Foundation\Condition
	 */
	interface ConditionInterface{

		/**
		 * @param \Jungle\Data\Foundation\Record\Properties\PropertyRegistryInterface|mixed $data
		 * @param null|ValueAccessAwareInterface|callable $access - if data is outer original data
		 * @return mixed
		 */
		public function __invoke($data, $access = null);

		/**
		 * @return array
		 */
		public function toStorageCondition();

	}
}

