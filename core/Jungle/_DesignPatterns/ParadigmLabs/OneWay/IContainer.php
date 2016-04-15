<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 15.11.2015
 * Time: 2:02
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\OneWay {

	/**
	 * Interface IItemCollection
	 * @package Jungle\TestPattern
	 *
	 *
	 * Этот контейнер не связан двунаправленной связью с предметом, поэтому предмет может фактически быть любым типом
	 *
	 */
	interface IContainer{

		/**
		 * @param $item
		 * @return $this
		 */
		public function addItem($item);

		/**
		 * @param $item
		 * @return mixed
		 */
		public function searchItem($item);

		/**
		 * @param $item
		 * @return $this
		 */
		public function removeItem($item);



	}
}

