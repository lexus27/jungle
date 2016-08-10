<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 18.05.2015
 * Time: 19:05
 */

namespace Jungle\Util\Smart\Value\Color {


	use Jungle\Util\Smart\Value\Color;
	use Jungle\Util\Smart\Value\IColorType;

	/**
	 * Class HSL
	 * @package Jungle\Util\Smart\Value\Color
	 */
	class HSL implements IColorType{

		/**
		 * @return string
		 */
		public function getName(){
			return 'hsl';
		}

		/**
		 * Выставить имя объекту
		 * @param $name
		 */
		public function setName($name){ }

		/**
		 * @param string|array $color
		 * @return bool|array [$hue, $saturation, $luminance, $alpha]
		 */
		public function parse($color){
			if(preg_match(
				'@hsla?\(([\d\.]+),([\d\.]+)%?,([\d\.]+)%?(,([\d\.]+)\)|\))@',
				str_replace([' ', "\r\n", "\t"], '', $color),
				$m
			)){
				$h = intval($m[1]);
				$s = intval($m[2]);
				$l = intval($m[3]);
				$a = isset($m[5])? floatval($m[5]) : 1.0;


				if($h > 360) $h = 360;
				elseif($h < 0) $h = 0;

				if($s > 100) $s = 360;
				elseif($s < 0) $s = 0;

				if($l > 100) $l = 360;
				elseif($l < 0) $l = 0;

				if($a > 1.0) $a = 1.0;
				elseif($a < 0.0) $a = 0.0;

				return [$h, $s, $l, $a];
			}
			return false;
		}

		/**
		 * @param int $hue [from 0 to 360 degree]
		 * @param int $saturation [percentage from 0 to 100]
		 * @param int $luminance [percentage from 0 to 100]
		 * @param float $alpha [from 0.0 to 1.0, double value]
		 * @return string $string_representation
		 */
		public function render($hue, $saturation, $luminance, $alpha = 1.0){
			if($hue > 360) $hue = 360;
			elseif($hue < 0) $hue = 0;

			if($saturation > 100) $saturation = 100;
			elseif($saturation < 0) $saturation = 0;
			$saturation = round($saturation,4);

			if($luminance > 100) $luminance = 100;
			elseif($luminance < 0) $luminance = 0;
			$luminance = round($luminance,4);

			if($alpha > 1.0) $alpha = 1.0;
			elseif($alpha < 0.0) $alpha = 0.0;


			$n = $this->getName();
			if($alpha < 1.0){
				$n .= 'a';
			}
			return $n . "({$hue},{$saturation}%,{$luminance}%" . ($alpha < 1.0 ? ",{$alpha})" : ')');
		}

		/**
		 * @param int $hue [from 0 to 360 degree]
		 * @param int $saturation [percentage from 0 to 100]
		 * @param int $luminance [percentage from 0 to 100]
		 * @param double $alpha [from 0.0 to 1.0, double value]
		 * @return string
		 */
		public function represent($hue, $saturation, $luminance, $alpha = 1.0){
			return [$hue, $saturation, $luminance, $alpha];
		}

	}
}