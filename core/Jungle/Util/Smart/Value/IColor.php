<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 18.05.2015
 * Time: 16:48
 */

namespace Jungle\Util\Smart\Value {

	/**
	 * Interface IColor
	 * @package Jungle\Util\Smart\Value
	 */
	interface IColor extends IValue{

		/**
		 * @param IColorType $type
		 * @return $this
		 */
		public function setType(IColorType $type);

		/**
		 * @return IColorType
		 */
		public function getType();


		/** @RGB */

		/**
		 * @param $color
		 * @return mixed
		 */
		public function setRed($color);

		/**
		 * @param int $count
		 * @return mixed
		 */
		public function redIncrement($count = 1);

		/**
		 * @param int $count
		 * @return mixed
		 */
		public function redDecrement($count = 1);

		/**
		 * @return int
		 */
		public function getRed();


		/**
		 * @param $color
		 * @return $this
		 */
		public function setGreen($color);

		/**
		 * @param int $count
		 * @return $this
		 */
		public function greenIncrement($count = 1);

		/**
		 * @param int $count
		 * @return $this
		 */
		public function greenDecrement($count = 1);

		/**
		 * @return int
		 */
		public function getGreen();


		/**
		 * @param $color
		 * @return $this
		 */
		public function setBlue($color);

		/**
		 * @param int $count
		 * @return $this
		 */
		public function blueIncrement($count = 1);

		/**
		 * @param int $count
		 * @return $this
		 */
		public function blueDecrement($count = 1);

		/**
		 * @return int
		 */
		public function getBlue();


		/** @CMY */


		/**
		 * @param $color
		 * @return $this
		 */
		public function setCyan($color);

		/**
		 * @param int $count
		 * @return $this
		 */
		public function cyanIncrement($count = 1);

		/**
		 * @param int $count
		 * @return $this
		 */
		public function cyanDecrement($count = 1);

		/**
		 * @return int
		 */
		public function getCyan();


		/**
		 * @param $color
		 * @return $this
		 */
		public function setMagenta($color);

		/**
		 * @param int $count
		 * @return $this
		 */
		public function magentaIncrement($count = 1);

		/**
		 * @param int $count
		 * @return $this
		 */
		public function magentaDecrement($count = 1);

		/**
		 * @return int
		 */
		public function getMagenta();


		/**
		 * @param $color
		 * @return $this
		 */
		public function setYellow($color);

		/**
		 * @param int $count
		 * @return $this
		 */
		public function yellowIncrement($count = 1);

		/**
		 * @param int $count
		 * @return $this
		 */
		public function yellowDecrement($count = 1);

		/**
		 * @return int
		 */
		public function getYellow();


		/** @HSL default  */


		/**
		 * @param int $hue
		 * @return $this
		 */
		public function setHue($hue);

		/**
		 * @return int
		 */
		public function getHue();


		/**
		 * @param int $saturation
		 * @return $this
		 */
		public function setSaturation($saturation);

		/**
		 * @return int
		 */
		public function getSaturation();


		/**
		 * @param int $light
		 * @return $this
		 */
		public function setLight($light);

		/**
		 * @return int
		 */
		public function getLight();


		/**
		 * @param float $alpha
		 * @return mixed
		 */
		public function setAlpha($alpha = 1.0);

		/**
		 * @return float
		 */
		public function getAlpha();


		/**
		 * @param $degree
		 * @return $this
		 */
		public function hueIncrement($degree);

		/**
		 * @param $degree
		 * @return $this
		 */
		public function hueDecrement($degree);


		/**
		 * @param $percentage
		 * @return $this
		 */
		public function saturate($percentage);

		/**
		 * @param $percentage
		 * @return $this
		 */
		public function desaturate($percentage);


		/**
		 * @param $percentage
		 * @return $this
		 */
		public function lighten($percentage);

		/**
		 * @param $percentage
		 * @return $this
		 */
		public function darken($percentage);


		/**
		 * @param $percentage
		 * @return $this
		 */
		public function fadeIn($percentage);

		/**
		 * @param $percentage
		 * @return $this
		 */
		public function fadeOut($percentage);

	}
}