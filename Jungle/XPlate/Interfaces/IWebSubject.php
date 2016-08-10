<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 09.10.2015
 * Time: 22:26
 */
namespace Jungle\XPlate\Interfaces {

	/**
	 * Interface IWebSubject
	 * @package Jungle\XPlate\Interfaces
	 *
	 * IWebStrategy.addSubject
	 * IWebStrategy.searchSubject
	 * IWebStrategy.removeSubject
	 *
	 */
	interface IWebSubject{

		public function setStrategy(IWebStrategy $strategy, $appliedInStrategy = false, $appliedInOld = false);

		public function getStrategy();

		public function refreshStrategy();
	}
}

