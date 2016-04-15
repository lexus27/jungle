<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.04.2016
 * Time: 19:02
 */
namespace Jungle\Util\Data\Map {


	/**
	 * Interface FieldOriginalAccessInterface
	 * @package Jungle\Util\Data
	 */
	interface FieldOriginalAccessInterface{

		public function setGetter($getter);

		public function getGetter();

		public function setSetter($setter);

		public function getSetter();

		public function setOriginalKey($original);

	}
}

