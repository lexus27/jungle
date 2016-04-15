<?php

namespace Jungle\Data\DataMap\Collection\Source {

	use Jungle\Util\Value\Callback;

	/**
	 * Class Converter
	 * @package Jungle\Data\DataMap
	 */
	class Converter{

		/**
		 * @param Converter|callable|null $converter
		 * @return callable|Converter|null
		 */
		public static function checkoutConverter(callable $converter = null){
			return Callback::checkoutCallableInstance('Converter',Converter::class,$converter);
		}

		/**
		 * @Callable
		 * @param $data
		 * @return mixed
		 */
		public function __invoke($data){
			return $data;
		}

	}
}

