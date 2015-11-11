<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.09.2015
 * Time: 20:59
 */
namespace Jungle\XPlate\Interfaces {

	use Jungle\XPlate\Services\AttributeManager;
	use Jungle\XPlate\Services\StyleManager;
	use Jungle\XPlate\Services\TagManager;

	/**
	 * Interface IStrategy
	 * @package Jungle\XPlate\Interfaces
	 */
	interface IWebStrategy{

		/**
		 * @return AttributeManager
		 */
		public function getAttributeManager();

		/**
		 * @return TagManager
		 */
		public function getTagManager();

		/**
		 * @return StyleManager
		 */
		public function getStyleManager();

		/**
		 * @return $this
		 */
		public function lock();

		/**
		 * @return bool
		 */
		public function isLocked();

		/**
		 * @return bool
		 */
		public function isValid();

		/**
		 * @return IWebStrategy
		 */
		public static function getDefault();

		/**
		 * @param IWebStrategy $strategy
		 * @return mixed
		 */
		public static function setDefault(IWebStrategy $strategy);


		public function addSubject(IWebSubject $subject, $appliedInSubject = false);

		public function searchSubject(IWebSubject $subject);

		public function removeSubject(IWebSubject $subject, $appliedInSubject = false);

	}
}

