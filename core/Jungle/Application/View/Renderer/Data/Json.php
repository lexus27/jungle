<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.07.2016
 * Time: 3:00
 */
namespace Jungle\Application\View\Renderer\Data {
	
	use Jungle\Application\View\Renderer\Data;

	/**
	 * Class Json
	 * @package Jungle\Application\View\Renderer\Data
	 */
	class Json extends Data{

		/**
		 * @param $data
		 * @return string
		 */
		public function convert($data){
			return json_encode($data);
		}

	}
}

