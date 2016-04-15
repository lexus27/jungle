<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.03.2016
 * Time: 0:57
 */
namespace Jungle\Data\DataMap\Collection\Source {

	use Jungle\Data\DataMap\Collection\Source;

	/**
	 * Class ArraySource
	 * @package Jungle\Data\DataMap\Collection\Source
	 */
	class ArraySource extends Source{

		/** @var array  */
		protected $records = [];

		public function __construct(array $records){
			$this->records = $records;
		}

	}
}

