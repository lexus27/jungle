<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:18
 */
namespace Jungle\Data\Foundation\Record\Collection {

	use Jungle\Data\Foundation\Schema\OuterInteraction\ValueAccessAwareInterface;
	use Jungle\Data\Foundation\Schema\OuterInteraction\ValueAccessor;
	use Jungle\Util\Value\Cmp as UtilCmp;

	/**
	 * Class Sorter
	 * @package Jungle\Data\Foundation\Record
	 */
	abstract class Sorter extends \Jungle\Data\Foundation\Collection\Sortable\Sorter implements SorterInterface{

		/** @var  array */
		protected $sort_fields;

		/** @var  ValueAccessAwareInterface */
		protected $accessor;

		/**
		 * @param array $items
		 * @return $this
		 */
		public function sort(array & $items){
			return usort(
				$items,
				function ($a, $b){
					$result = 0;
					$fields = $this->getSortFields();
					foreach($fields as $field_name => $direction){
						$valueA = ValueAccessor::handleAccessGet($this->accessor, $a, $field_name);
						$valueB = ValueAccessor::handleAccessGet($this->accessor, $b, $field_name);
						$result = call_user_func($this->cmp, $valueA, $valueB);
						if($result !== 0){
							$result = $direction === 'DESC' ? UtilCmp::invert($result) : $result;
							break;
						}
					}
					return $result;
				}
			);
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

	}
}

