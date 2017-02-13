<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 18.05.2015
 * Time: 16:47
 */

namespace Jungle\Util\Smart\Value {


	/**
	 * Class Color
	 * @package Jungle\Util\Smart\Value
	 */
	class Color extends Value implements IColor{

		const HSL_HUE         = 0;
		const HSL_SATURATION  = 1;
		const HSL_LUMINANCE   = 2;
		const HSL_ALPHA       = 3;

		const RGB_RED    = 0;
		const RGB_GREEN  = 1;
		const RGB_BLUE   = 2;
		const RGB_ALPHA  = 3;

		const CMY_CYAN    = 0;
		const CMY_MAGENTA = 1;
		const CMY_YELLOW  = 2;
		const CMY_ALPHA   = 3;

		/**
		 * @var array
		 */
		protected static $default_value = [0,0,0,1.0];

		/**
		 * @var IColorType[]
		 */
		protected static $color_types = [];


		/**
		 * @var IColorType
		 */
		protected $type;

		/**
		 * @param IColorType $type
		 * @return $this
		 */
		public function setType(IColorType $type){
			if($this->type !== $type){
				$this->type = $type;
			}
			return $this;
		}

		/**
		 * @return IColorType
		 */
		public function getType(){
			return $this->type;
		}

		/**
		 * @param string|array $value
		 * @return $this
		 */
		public function setValue($value){
			if(!is_array($value)){
				$data = $this->parseColorString($value, $type);
				if($data === false){
					throw new \LogicException('Color.setValue type is not detected for passed value "' . $value . '" ');
				}else{
					$value = $data;
				}
				$cnt = count($value);
				if($cnt !== 4){
					if($cnt === 3){
						$value[] = 1.0;
					}else{
						throw new \LogicException('Color.setValue type is invalid parser returned (array must be count is 4 [h,s,l,a])');
					}
				}
				$this->setType($type);
			}
			return parent::setValue($value);
		}


		/**
		 * @param $string
		 * @param null $colorType
		 * @return array|bool
		 */
		protected function parseColorString($string, & $colorType = null){
			foreach(self::$color_types as $colorType){
				$data = $colorType->parse($string);
				if($data !== false){
					return $data;
				}
			}
			return false;
		}

		/**
		 * @return string
		 */
		public function getValue(){
			$val = $this->getRaw();
			return $this->type->render(
				$val[self::HSL_HUE],
				$val[self::HSL_SATURATION],
				$val[self::HSL_LUMINANCE],
				$val[self::HSL_ALPHA]
			);
		}


		/**
		 * @return string
		 */
		public function getRepresent(){
			$this->getRaw();
			return $this->type->represent(
				$this->value[self::HSL_HUE],
				$this->value[self::HSL_SATURATION],
				$this->value[self::HSL_LUMINANCE],
				$this->value[self::HSL_ALPHA]
			);
		}

		/**
		 * @return array
		 */
		public function getHSL(){
			return $this->getRaw();
		}

		/**
		 * @param $raw1
		 * @param $raw2
		 * @return bool
		 */
		protected function compareRaw($raw1,$raw2){
			return $raw1 === $raw2 || ( is_array($raw1) && is_array($raw2) && (
				$raw1[self::HSL_HUE]        === $raw2[self::HSL_HUE] &&
				$raw1[self::HSL_SATURATION] === $raw2[self::HSL_SATURATION] &&
				$raw1[self::HSL_LUMINANCE]  === $raw2[self::HSL_LUMINANCE] &&
				$raw1[self::HSL_ALPHA]      === $raw2[self::HSL_ALPHA]
			));
		}

		/**
		 * @param IValue|mixed $value
		 * @return bool
		 */
		public function equal($value){
			if($value instanceof Color){
				return static::compareRaw($this->getRaw(),$value->getRaw());
			}else if(is_string($value)){
				return static::compareRaw($this->getRaw(), $this->parseColorString($value));
			}
			return false;
		}

		/**
		 * @param Color $descendant
		 */
		protected function onDelivery(Color $descendant){
			if($this->type){
				$descendant->setType($this->type);
			}
		}

		/**
		 * Наследование происходит только при любом вызове getRaw|getValue - активизируется цепочка наследования
		 * Событие вызывается сразу после начала активности $this->extending и до вызова конфигуратора
		 */
		protected function beforeExtenderCall(){
			if($this->ancestor instanceof Color){
				$this->type = $this->ancestor->getType();
			}
		}

		/** @HSL */


		/**
		 * @param int $hue
		 * @return $this
		 */
		public function setHue($hue){
			return $this->_hslManipulation(self::HSL_HUE, $hue, 360, true);
		}

		/**
		 * @return mixed
		 */
		public function getHue(){
			return $this->_hslManipulation(self::HSL_HUE);
		}

		/**
		 * @param int $degree
		 * @return $this
		 */
		public function hueIncrement($degree = 1){
			return $this->_hslManipulation(self::HSL_HUE, $degree, 360, true, true, true);
		}

		/**
		 * @param int $degree
		 * @return $this
		 */
		public function hueDecrement($degree = 1){
			return $this->_hslManipulation(self::HSL_HUE, $degree, 360, true, true, false);
		}




		/**
		 * @param int $saturation
		 * @return Color
		 */
		public function setSaturation($saturation){
			return $this->_hslManipulation(self::HSL_SATURATION, $saturation, 100, true);
		}

		/**
		 * @return mixed
		 */
		public function getSaturation(){
			return $this->_hslManipulation(self::HSL_SATURATION);
		}

		/**
		 * @param int $percentage
		 * @return Color
		 */
		public function saturate($percentage = 1){
			return $this->_hslManipulation(self::HSL_SATURATION, $percentage, 100, false, true, true);
		}

		/**
		 * @param int $percentage
		 * @return Color
		 */
		public function desaturate($percentage = 1){
			return $this->_hslManipulation(self::HSL_SATURATION, $percentage, 100, false, true, false);
		}


		/**
		 * @param int $lightness
		 * @return Color
		 */
		public function setLight($lightness){
			return $this->_hslManipulation(self::HSL_LUMINANCE, $lightness);
		}

		/**
		 * @return mixed
		 */
		public function getLight(){
			return $this->_hslManipulation(self::HSL_LUMINANCE);
		}

		/**
		 * @param int $percentage
		 * @return Color
		 */
		public function lighten($percentage = 1){
			return $this->_hslManipulation(self::HSL_LUMINANCE, $percentage, 100, false, true, true);
		}

		/**
		 * @param int $percentage
		 * @return Color
		 */
		public function darken($percentage = 1){
			return $this->_hslManipulation(self::HSL_LUMINANCE, $percentage, 100, false,true,false);
		}




		/**
		 * @param float $alpha
		 * @return Color
		 */
		public function setAlpha($alpha = 1.0){
			return $this->_hslManipulation(self::HSL_ALPHA, $alpha, 1.0);
		}

		/**
		 * @return float
		 */
		public function getAlpha(){
			return $this->_hslManipulation(self::HSL_ALPHA);
		}

		/**
		 * @param int $percentage
		 * @return Color
		 */
		public function fadeIn($percentage = 1){
			return $this->_hslManipulation(self::HSL_ALPHA, $percentage / 100, 1.0, false, true, true);
		}

		/**
		 * @param int $percentage
		 * @return Color
		 */
		public function fadeOut($percentage = 1){
			return $this->_hslManipulation(self::HSL_ALPHA,$percentage / 100, 1.0, false, true, false);
		}


		/** @RGB */

		/**
		 * @param $color
		 * @return mixed
		 */
		public function setRed($color){
			return $this->_rgbManipulation(self::RGB_RED, $color);
		}

		/**
		 * @param int $count
		 * @return mixed
		 */
		public function redIncrement($count = 1){
			return $this->_rgbManipulation(self::RGB_RED, $count, true, true);
		}

		/**
		 * @param int $count
		 * @return mixed
		 */
		public function redDecrement($count = 1){
			return $this->_rgbManipulation(self::RGB_RED, $count, true, false);
		}

		/**
		 * @return int
		 */
		public function getRed(){
			return $this->_rgbManipulation(self::RGB_RED);
		}

		/**
		 * @param $color
		 * @return $this
		 */
		public function setGreen($color){
			return $this->_rgbManipulation(self::RGB_GREEN, $color);
		}

		/**
		 * @param int $count
		 * @return $this
		 */
		public function greenIncrement($count = 1){
			return $this->_rgbManipulation(self::RGB_GREEN, $count, true, true);
		}

		/**
		 * @param int $count
		 * @return $this
		 */
		public function greenDecrement($count = 1){
			return $this->_rgbManipulation(self::RGB_GREEN, $count,true,false);
		}

		/**
		 * @return int
		 */
		public function getGreen(){
			return $this->_rgbManipulation(self::RGB_GREEN);
		}

		/**
		 * @param $color
		 * @return $this
		 */
		public function setBlue($color){
			return $this->_rgbManipulation(self::RGB_BLUE, $color);
		}

		/**
		 * @param int $count
		 * @return $this
		 */
		public function blueIncrement($count = 1){
			return $this->_rgbManipulation(self::RGB_BLUE, $count, true, true);
		}

		/**
		 * @param int $count
		 * @return $this
		 */
		public function blueDecrement($count = 1){
			return $this->_rgbManipulation(self::RGB_BLUE,$count,true,false);
		}

		/**
		 * @return int
		 */
		public function getBlue(){
			return $this->_rgbManipulation(self::RGB_BLUE);
		}


		/** @CMY */

		/**
		 * @param $color
		 * @return $this
		 */
		public function setCyan($color){
			return $this->_cmyManipulation(self::CMY_CYAN, $color);
		}

		/**
		 * @param int $count
		 * @return $this
		 */
		public function cyanIncrement($count = 1){
			return $this->_cmyManipulation(self::CMY_CYAN, $count, true, true);
		}

		/**
		 * @param int $count
		 * @return $this
		 */
		public function cyanDecrement($count = 1){
			return $this->_cmyManipulation(self::CMY_CYAN, $count, true, false);
		}

		/**
		 * @return int
		 */
		public function getCyan(){
			return $this->_cmyManipulation(self::CMY_CYAN);
		}

		/**
		 * @param $color
		 * @return $this
		 */
		public function setMagenta($color){
			return $this->_cmyManipulation(self::CMY_MAGENTA, $color);
		}

		/**
		 * @param int $count
		 * @return $this
		 */
		public function magentaIncrement($count = 1){
			return $this->_cmyManipulation(self::CMY_MAGENTA, $count, true, true);
		}

		/**
		 * @param int $count
		 * @return $this
		 */
		public function magentaDecrement($count = 1){
			return $this->_cmyManipulation(self::CMY_MAGENTA, $count, true, false);
		}

		/**
		 * @return int
		 */
		public function getMagenta(){
			return $this->_cmyManipulation(self::CMY_MAGENTA);
		}

		/**
		 * @param $color
		 * @return $this
		 */
		public function setYellow($color){
			return $this->_cmyManipulation(self::CMY_YELLOW,$color);
		}

		/**
		 * @param int $count
		 * @return $this
		 */
		public function yellowIncrement($count = 1){
			return $this->_cmyManipulation(self::CMY_YELLOW,$count,true,true);
		}

		/**
		 * @param int $count
		 * @return $this
		 */
		public function yellowDecrement($count = 1){
			return $this->_cmyManipulation(self::CMY_YELLOW,$count,true,false);
		}

		/**
		 * @return int
		 */
		public function getYellow(){
			return $this->_cmyManipulation(self::CMY_YELLOW);
		}


		/**
		 * @param int $i
		 * @param null $value
		 * @param int $maxValue
		 * @param bool $isCyclic
		 * @param bool $offset
		 * @param null $increase
		 * @return $this
		 */
		protected function _hslManipulation($i, $value = null, $maxValue = 100, $isCyclic = false, $offset = false, $increase = null){
			$v = $this->getRaw();
			if($value === null){
				return $v[$i];
			}elseif($offset && $increase === null){
				$v[$i] += $value;
			}elseif($offset && $increase === true){
				$v[$i] += abs($value);
			}elseif($offset && $increase === false){
				$v[$i] -= abs($value);
			}else{
				$v[$i] = $value;
			}
			if($isCyclic){
				if($v[$i] > $maxValue){
					$v[$i] = ($v[$i] % ($maxValue + 1));
				}elseif($v[$i] < 0){
					$v[$i] = ($v[$i] % ($maxValue + 1));
					if($v[$i] < 0){
						$v[self::HSL_HUE] += $maxValue;
					}
				}
			}else{
				if($v[$i] > $maxValue){
					$v[$i] = $maxValue;
				}elseif($v[$i] < 0.0){
					$v[$i] = 0.0;
				}
			}
			$this->setValue($v);
			return $this;
		}

		/**
		 * @CMY-manipulation
		 * @param int $i color index in represent array
		 * @param null $value
		 * @param bool $offset
		 * @param null $increase
		 * @return $this
		 */
		protected function _cmyManipulation($i,$value = null, $offset = false, $increase = null){
			$v = $this->getRaw();
			$rgb = Color::HSLtoRGB($v[0], $v[1], $v[2], $v[3]);
			$cmy = Color::RGBtoCMY($rgb[0], $rgb[1], $rgb[2], $rgb[3]);
			if($value === null){
				return $cmy[$i];
			}elseif($offset && $increase === null){
				$cmy[$i] += $value;
			}elseif($offset && $increase === true){
				$cmy[$i] += abs($value);
			}elseif($offset && $increase === false){
				$cmy[$i] -= abs($value);
			}else{
				$cmy[$i] = $value;
			}

			if($cmy[$i] > 100) $cmy[$i] = 100;
			elseif($cmy[$i] < 0) $cmy[$i] = 0;

			$rgb = Color::CMYtoRGB($cmy[0], $cmy[1], $cmy[2], $cmy[3]);
			$this->setValue(Color::RGBtoHSL($rgb[0], $rgb[1], $rgb[2], $rgb[3]));
			return $this;
		}

		/**
		 * @param $i
		 * @param null $value
		 * @param bool $offset
		 * @param null $increase
		 * @return $this
		 */
		protected function _rgbManipulation($i, $value = null, $offset = false, $increase = null){
			$v = $this->getRaw();
			$rgb = Color::HSLtoRGB($v[0], $v[1], $v[2], $v[3]);
			if($value === null){
				return $rgb[$i];
			}elseif($offset && $increase === null){
				$rgb[$i] += $value;
			}elseif($offset && $increase === true){
				$rgb[$i] += abs($value);
			}elseif($offset && $increase === false){
				$rgb[$i] -= abs($value);
			}else{
				$rgb[$i] = $value;
			}

			if($rgb[$i] > 255) $rgb[$i] = 255;
			elseif($rgb[$i] < 0) $rgb[$i] = 0;

			$this->setValue(Color::RGBtoHSL($rgb[0], $rgb[1], $rgb[2], $rgb[3]));
			return $this;
		}



		/**
		 * @param IColorType $type
		 */
		public static function addColorType(IColorType $type){
			$i = self::searchColorType($type);
			if($i === false){
				self::$color_types[] = $type;
			}
		}

		/**
		 * @param IColorType $type
		 * @return bool|int
		 */
		public static function searchColorType(IColorType $type){
			return array_search($type,self::$color_types,true);
		}

		/**
		 * @param IColorType $type
		 */
		public static function removeColorType(IColorType $type){
			$i = self::searchColorType($type);
			if($i!==false){
				array_splice(self::$color_types,$i,1);
			}
		}

		/**
		 * @param $name
		 * @return IColorType|null
		 */
		public static function getColorType($name){
			foreach(self::$color_types as $type){
				if(strcasecmp($type->getName(),$name) === 0){
					return $type;
				}
			}
			return null;
		}


		/**
		 * @param $red
		 * @param $green
		 * @param $blue
		 * @param float $alpha
		 * @return array
		 */
		public static function RGBtoHSL($red, $green, $blue, $alpha = 1.0){
			$red    /= 255;
			$green  /= 255;
			$blue   /= 255;

			$min = min($red, $green, $blue);
			$max = max($red, $green, $blue);
			$delta = $max - $min;

			$Luminance = ($max + $min) / 2;

			if($delta == 0){
				$Hue        = 0;
			    $Saturation = 0;
			}else{
				$Saturation = $Luminance < 0.5? $delta / ($max + $min): $delta / (2 - $max - $min) ;

			    $deltaRed    = ((($max - $red) / 6) + ($delta / 2)) / $delta;
			    $deltaGreen  = ((($max - $green) / 6) + ($delta / 2)) / $delta;
			    $deltaBlue   = ((($max - $blue) / 6) + ($delta / 2)) / $delta;

			    if($red == $max){
				    $Hue = $deltaBlue - $deltaGreen;
			    }else if($green == $max){
				    $Hue = (1 / 3) + $deltaRed - $deltaBlue;
			    }else if($blue == $max){
				    $Hue = (2 / 3) + $deltaGreen - $deltaRed;
			    }else{
				    $Hue = 0;
			    }
			    if($Hue < 0) $Hue += 1;
			    if($Hue > 1) $Hue -= 1;
			}

			return [$Hue * 360, $Saturation * 100, $Luminance * 100, $alpha];
		}


		/**
		 * @param $hue
		 * @param $saturation
		 * @param $luminance
		 * @param float $alpha
		 * @return array
		 */
		public static function HSLtoRGB($hue,$saturation,$luminance, $alpha = 1.0){
			static $function;if($function === null){
				$function = function($_1, $_2, $Hue){
				   if($Hue < 0) $Hue += 1;
				   if($Hue > 1) $Hue -= 1;
				   if((6 * $Hue) < 1) return ($_1 + ($_2 - $_1) * 6 * $Hue);
				   if((2 * $Hue) < 1) return ($_2);
				   if((3 * $Hue) < 2) return ($_1 + ($_2 - $_1) * ((2 / 3) - $Hue) * 6);
				   return ($_1);
				};
			}

			$hue/=360;
			$saturation/=100;
			$luminance/=100;
			if($saturation == 0){
				$Red    = $luminance * 255;
			    $Green  = $luminance * 255;
			    $Blue   = $luminance * 255;
			}else{
				if($luminance < 0.5){
					$_2 = $luminance * (1 + $saturation);
				}else{
				    $_2 = ($luminance + $saturation) - ($saturation * $luminance);
			    }

			    $_1 = 2 * $luminance - $_2;

				$Red    = 255 * $function($_1, $_2, $hue + (1 / 3));
				$Green  = 255 * $function($_1, $_2, $hue);
				$Blue   = 255 * $function($_1, $_2, $hue - (1 / 3));
			}
			return [$Red, $Green, $Blue, $alpha];
		}

		/**
		 * @param $red
		 * @param $green
		 * @param $blue
		 * @param float $alpha
		 * @return string
		 */
		public static function RGBtoHEX($red,$green,$blue, $alpha = 1.0){
			return [
				str_pad(dechex($red), 2, '0', STR_PAD_LEFT),
				str_pad(dechex($green), 2, '0', STR_PAD_LEFT),
				str_pad(dechex($blue), 2, '0', STR_PAD_LEFT),
			];
		}

		/**
		 * @param $hex
		 * @return array
		 */
		public static function HEXtoRGB($hex){
			$length = strlen($hex);

			if($length < 5){
				$rgb = [
					substr($hex, 0, 1),
					substr($hex, 1, 1),
					substr($hex, 2, 1)
				];
			}else if($length > 5){
				$rgb = [
					substr($hex, 0, 2),
					substr($hex, 2, 2),
					substr($hex, 4, 2)
				];
			}else{
				return [0,0,0,1.0];
			}

			foreach($rgb as &$code){
				$code = hexdec(strlen($code)===1?$code.$code:$code);
			}
			$rgb[] = 1.0;

			return $rgb;
		}


		/**
		 * @param $r
		 * @param $g
		 * @param $b
		 * @param float $a
		 * @return array
		 */
		public static function RGBtoCMY($r,$g,$b, $a = 1.0){
			$c = (1 - ($r / 255)) * 100;
			$m = (1 - ($g / 255)) * 100;
			$y = (1 - ($b / 255)) * 100;
			return [$c, $m, $y, $a];
		}

		/**
		 * @param $c
		 * @param $m
		 * @param $y
		 * @param float $a
		 * @return array
		 */
		public static function CMYtoRGB($c,$m,$y,$a = 1.0){
			$r = (1 - ($c / 100)) * 255;
			$g = (1 - ($m / 100)) * 255;
			$b = (1 - ($y / 100)) * 255;
			return [$r, $g, $b, $a];
		}

		/**
		 * @param $c
		 * @param $m
		 * @param $y
		 * @param float $a
		 * @return array
		 */
		public static function CMYtoCMYK($c, $m, $y, $a = 1.0){
			//CMYK and CMY values from 0 to 1
			$c = $c / 100;
			$m = $m / 100;
			$y = $y / 100;

			$k = 1;
			if($c < $k) $k = $c;
			if($m < $k) $k = $m;
			if($y < $k) $k = $y;
			if($k == 1){ //Black
				$c = 0;
			    $m = 0;
			    $y = 0;
			}else{
				$c = ($c - $k) / (1 - $k);
			    $m = ($m - $k) / (1 - $k);
			    $y = ($y - $k) / (1 - $k);
			}
			return [$c * 100,$m * 100,$y * 100,$k * 100,$a];
		}

		/**
		 * @param $c
		 * @param $m
		 * @param $y
		 * @param $k
		 * @param float $a
		 * @return array
		 */
		public static function CMYKtoCMY($c, $m, $y, $k, $a = 1.0){
			$c = $c / 100;
			$m = $m / 100;
			$y = $y / 100;
			$k = $k / 100;
			//CMYK and CMY values from 0 to 1
			$c = ($c * (1 - $k) + $k);
			$m = ($m * (1 - $k) + $k);
			$y = ($y * (1 - $k) + $k);

			return [$c,$m,$y,$a];
		}

	}
}