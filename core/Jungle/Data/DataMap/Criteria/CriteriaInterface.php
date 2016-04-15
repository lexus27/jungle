<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 18:58
 */
namespace Jungle\Data\DataMap\Criteria {

	use Jungle\Data\DataMap\ValueAccess\Getter;

	/**
	 * Interface CriteriaInterface
	 */
	interface CriteriaInterface{

		/**
		 * @param $item
		 * @param \Jungle\Data\DataMap\ValueAccess\ValueAccessAwareInterface|Getter|callable $access
		 * @return bool
		 */
		public function __invoke($item, $access);

	}

}

