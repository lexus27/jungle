<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.07.2016
 * Time: 21:32
 */
namespace Jungle\Application\View\Renderer\Data {
	
	use Jungle\Application\View\Renderer\Data;

	/**
	 * Class PhpSerialized
	 * @package Jungle\Application\View\Renderer\Data
	 */
	class PhpSerialized extends Data{
		
		/**
		 * @param $data
		 * @return string
		 */
		public function convert($data){
			return serialize($data);
		}

	}
}

