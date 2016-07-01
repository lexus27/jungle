<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:18
 */
namespace Jungle\Data\Record\Collection {

	use Jungle\Data\Record\Head\Schema;
	use Jungle\Util\Data\Foundation\Schema\OuterInteraction\ValueAccessAwareInterface;
	use Jungle\Util\Data\Foundation\Schema\OuterInteraction\ValueAccessor;
	use Jungle\Util\Value\Cmp as UtilCmp;

	/**
	 * Class Sorter
	 * @package Jungle\Data\Record
	 */
	class Sorter extends \Jungle\Util\Data\Foundation\Collection\Sortable\Sorter implements SorterInterface{

		/** @var  array */
		protected $sort_fields;

		/** @var  ValueAccessAwareInterface */
		protected $accessor;

		/**
		 * @param array $items
		 * @return $this
		 */
		public function sort(array & $items){
			return usort($items, $this);
		}

		/**
		 * @param $fields
		 * @return $this
		 */
		public function setSortFields($fields){
			$this->sort_fields = $fields;
			return $this;
		}

		/**
		 * @return array|null
		 */
		public function getSortFields(){
			return $this->sort_fields;
		}

		/**
		 * @param Schema $schema
		 * @return array
		 */
		public function toStorageSortFields(Schema $schema){
			$storage_sort_fields = [];
			foreach($this->sort_fields as $field_name => $direction){
				$storage_sort_fields[$field_name] = $direction;
			}
			return $storage_sort_fields;
		}

		/**
		 * @param ValueAccessAwareInterface $accessor
		 * @return $this
		 */
		public function setAccess(ValueAccessAwareInterface $accessor = null){
			$this->accessor = $accessor;
			return $this;
		}

		/**
		 * @return ValueAccessAwareInterface
		 */
		public function getAccess(){
			return $this->accessor;
		}

		/**
		 * @param $a
		 * @param $b
		 * @return int
		 */
		public function __invoke($a, $b){
			$result = 0;
			$fields = $this->getSortFields();
			foreach($fields as $field_name => $direction){
				$valueA = ValueAccessor::handleAccessGet($this->accessor, $a, $field_name);
				$valueB = ValueAccessor::handleAccessGet($this->accessor, $b, $field_name);
				$result = call_user_func($this->getCmp(), $valueA, $valueB);
				if($result !== 0){
					$result = $direction === 'DESC' ? UtilCmp::invert($result) : $result;
					break;
				}
			}
			return $result;
		}

	}
}

