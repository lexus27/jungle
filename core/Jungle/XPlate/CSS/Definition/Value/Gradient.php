<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 20.05.2015
 * Time: 2:54
 */

namespace Jungle\XPlate\CSS\Definition\Value;
use Jungle\Util\Smart\Value\IColor;
use Jungle\XPlate\WebEngineSet;

/**
 * Class Gradient
 * @package Jungle\XPlate\CSS\Definition\Value
 */
class Gradient {

	const TYPE_RADIAL = 1;

	const TYPE_LINEAR = 2;

	/**
	 * @var array
	 */
	protected $color_map = [];

	/**
	 * @var bool
	 */
	protected $color_map_sorted = false;

	/**
	 * @var mixed
	 * Тип градиента, радиальный, линейный
	 */
	protected $type = self::TYPE_LINEAR;

	/**
	 * @var mixed
	 * Направления градиента от 0% до 100%
	 * from left to right
	 * from right to left
	 *
	 * from top to bottom
	 * from bottom to top
	 *
	 * from top-left to bottom-right
	 * from top-right to bottom-left
	 * from bottom-left to top-right
	 * from bottom-right to top-left
	 *
	 */
	protected $direction;

	/**
	 * @param int $percentage
	 * @param \Jungle\Util\Smart\Value\IColor $color
	 * @return $this
	 */
	public function setColor($percentage,IColor $color){
		$percentage = intval($percentage);
		if($percentage > 100 || $percentage < 0){
			throw new \InvalidArgumentException('percentage out of range 0-100 passed "'. $percentage.'"');
		}
		$this->color_map[$percentage] = $color;
		return $this;
	}

	/**
	 * @param $type
	 * @return $this
	 */
	public function setType($type){
		$this->type = $type;
		return $this;
	}

	public function setDirection(){

	}

	public function getValue(){
		if(!$this->color_map_sorted){
			ksort($this->color_map);
			$this->color_map_sorted = true;
		}
		$a = [];
		$p = [
			'-o-gradient',
			'-webkit-linear-gradient',
			'-ms-gradient',
			'
			background: rgb(184,225,252); /* Old browsers */
/* IE9 SVG, needs conditional override of \'filter\' to \'none\' */
background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIxMDAlIiB5Mj0iMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2I4ZTFmYyIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwJSIgc3RvcC1jb2xvcj0iI2E5ZDJmMyIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjI1JSIgc3RvcC1jb2xvcj0iIzkwYmFlNCIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjM3JSIgc3RvcC1jb2xvcj0iIzkwYmNlYSIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjUwJSIgc3RvcC1jb2xvcj0iIzkwYmZmMCIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjUxJSIgc3RvcC1jb2xvcj0iIzZiYThlNSIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjgzJSIgc3RvcC1jb2xvcj0iI2EyZGFmNSIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNiZGYzZmQiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
background: -moz-linear-gradient(left,  rgb(184,225,252) 0%, rgb(169,210,243) 10%, rgb(144,186,228) 25%, rgb(144,188,234) 37%, rgb(144,191,240) 50%, rgb(107,168,229) 51%, rgb(162,218,245) 83%, rgb(189,243,253) 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right top, color-cancel(0%,rgb(184,225,252)), color-cancel(10%,rgb(169,210,243)), color-cancel(25%,rgb(144,186,228)), color-cancel(37%,rgb(144,188,234)), color-cancel(50%,rgb(144,191,240)), color-cancel(51%,rgb(107,168,229)), color-cancel(83%,rgb(162,218,245)), color-cancel(100%,rgb(189,243,253))); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(left,  rgb(184,225,252) 0%,rgb(169,210,243) 10%,rgb(144,186,228) 25%,rgb(144,188,234) 37%,rgb(144,191,240) 50%,rgb(107,168,229) 51%,rgb(162,218,245) 83%,rgb(189,243,253) 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(left,  rgb(184,225,252) 0%,rgb(169,210,243) 10%,rgb(144,186,228) 25%,rgb(144,188,234) 37%,rgb(144,191,240) 50%,rgb(107,168,229) 51%,rgb(162,218,245) 83%,rgb(189,243,253) 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(left,  rgb(184,225,252) 0%,rgb(169,210,243) 10%,rgb(144,186,228) 25%,rgb(144,188,234) 37%,rgb(144,191,240) 50%,rgb(107,168,229) 51%,rgb(162,218,245) 83%,rgb(189,243,253) 100%); /* IE10+ */
background: linear-gradient(to right,  rgb(184,225,252) 0%,rgb(169,210,243) 10%,rgb(144,186,228) 25%,rgb(144,188,234) 37%,rgb(144,191,240) 50%,rgb(107,168,229) 51%,rgb(162,218,245) 83%,rgb(189,243,253) 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#b8e1fc\', endColorstr=\'#bdf3fd\',GradientType=1 ); /* IE6-8 */


			'
		];

		$values = [];
		$engines = WebEngineSet::getDefault()->getEngines();
		foreach($this->color_map as $percentage => $color){
			foreach($engines as $engine){
				$vendor = $engine->getVendor();
				$values[] = 'linear-gradient';
				if(($gradient = $engine->getOption('gradient',null))){
					foreach($gradient['prefixes'] as $prefix){

					}
				}
			}
		}

	}


}