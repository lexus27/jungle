<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 18.05.2015
 * Time: 20:22
 */

namespace Jungle\Util\Smart\Value\Color {

	use Jungle\Util\Smart\Value\Color;
	use Jungle\Util\Smart\Value\IColorType;


	/**
	 * Class RGB
	 * @package Jungle\Util\Smart\Value\Color
	 */
	class RGB implements IColorType{

		/**
		 * @param string|array $color
		 * @return bool|array [$hue, $saturation, $luminance, $alpha]
		 */
		public function parse($color){
			if(preg_match(
				'@rgba?\(([\d\.]+),([\d\.]+),([\d\.]+)(,([\d\.]+)\)|\))@',
				str_replace([' ', "\r\n", "\t"], '', $color), $m
			)){
				$r = floatval($m[1]);
				$g = floatval($m[2]);
				$b = floatval($m[3]);
				$a = isset($m[5]) ? floatval($m[5]) : 1.0;

				if($r > 255) $r = 255;
				elseif($r < 0) $r = 0;

				if($g > 255) $g = 255;
				elseif($g < 0) $g = 0;

				if($b > 255) $b = 255;
				elseif($b < 0) $b = 0;

				if($a > 1.0) $a = 1.0;
				elseif($a < 0.0) $a = 0.0;

				return Color::RGBToHSL($r, $g, $b, $a);
			}
			return false;
		}

		/**
		 * @param int $hue [from 0 to 360 degree]
		 * @param int $saturation [percentage from 0 to 100]
		 * @param int $luminance [percentage from 0 to 100]
		 * @param double $alpha [from 0.0 to 1.0, double value]
		 * @return string $string_representation
		 */
		public function render($hue, $saturation, $luminance, $alpha = 1.0){
			$rgb = Color::HSLtoRGB($hue, $saturation, $luminance);
			$rgb[0] = round($rgb[0]);
			$rgb[1] = round($rgb[1]);
			$rgb[2] = round($rgb[2]);

			if($alpha < 1.0){
				return "rgba({$rgb[0]},{$rgb[1]},{$rgb[2]},{$alpha})";
			}else{
				return "rgb({$rgb[0]},{$rgb[1]},{$rgb[2]})";
			}

		}

		/**
		 * @param int $hue [from 0 to 360 degree]
		 * @param int $saturation [percentage from 0 to 100]
		 * @param int $luminance [percentage from 0 to 100]
		 * @param double $alpha [from 0.0 to 1.0, double value]
		 * @return array
		 */
		public function represent($hue, $saturation, $luminance, $alpha = 1.0){
			return Color::HSLtoRGB($hue, $saturation, $luminance, $alpha);
		}

		/**
		 * Получить имя объекта
		 * @return mixed
		 */
		public function getName(){
			return 'rgb';
		}

		/**
		 * Выставить имя объекту
		 * @param $name
		 */
		public function setName($name){}

	}
}