<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:16
 */
namespace Jungle\Data\Record\Collection {

	use Jungle\Util\Data\Schema\OuterInteraction\ValueAccessAwareInterface;

	/**
	 * Interface SorterInterface
	 * @package Jungle\Data\Record
	 */
	interface SorterInterface{

		/**
		 * @param ValueAccessAwareInterface|null $access
		 * @return mixed
		 */
		public function setAccess(ValueAccessAwareInterface $access = null);

		/**
		 * @return mixed
		 */
		public function getAccess();

		/**
		 * @param $fields
		 * @return mixed
		 */
		public function setSortFields($fields);

		/**
		 * @return mixed
		 */
		public function getSortFields();

	}
}

