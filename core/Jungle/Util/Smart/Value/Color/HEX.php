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
	 * Class HEX
	 * @package Jungle\Util\Smart\Value\Color
	 */
	class HEX implements IColorType{

		/**
		 * @param string|array $color
		 * @return bool|array [$hue, $saturation, $luminance, $alpha]
		 */
		public function parse($color){
			$color = strtolower(str_replace([' ', "\r\n", "\t"], '', $color));
			if(preg_match(
				'@^\#(([\w]{2})([\w]{2})([\w]{2})([\w]{2})?)|(([\w]{1})([\w]{1})([\w]{1})([\w]{1})?)$@',
				$color,$m
			)){
				$rgb = Color::HEXtoRGB($m[1]);
				$hsl = Color::RGBtoHSL($rgb[0], $rgb[1], $rgb[2], $rgb[3]);
				return $hsl;
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
			$rgb = Color::HSLtoRGB($hue,$saturation,$luminance);
			return '#' . implode('',Color::RGBtoHEX($rgb[0], $rgb[1], $rgb[2], $alpha));
		}

		/**
		 * @param int $hue [from 0 to 360 degree]
		 * @param int $saturation [percentage from 0 to 100]
		 * @param int $luminance [percentage from 0 to 100]
		 * @param double $alpha [from 0.0 to 1.0, double value]
		 * @return array
		 */
		public function represent($hue, $saturation, $luminance, $alpha = 1.0){
			$rgb = Color::HSLtoRGB($hue, $saturation, $luminance, $alpha);
			return Color::RGBtoHEX($rgb[0], $rgb[1], $rgb[2], $alpha);
		}

		/**
		 * Получить имя объекта
		 * @return mixed
		 */
		public function getName(){
			return 'hex';
		}

		/**
		 * Выставить имя объекту
		 * @param $name
		 */
		public function setName($name){}

	}
}