<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 24.06.2015
 * Time: 23:12
 */

namespace Jungle\XPlate\Interfaces{

	use Jungle\Basic\INamedBase;

	interface IMarker extends INamedBase{

		public function getQuerySymbol();

		public function getQueryString();

		public function __toString();
	}
}
