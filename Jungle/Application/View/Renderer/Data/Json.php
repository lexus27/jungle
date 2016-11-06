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

		/** @var string  */
		protected $type = 'json';

		/** @var string  */
		protected $mime_type = 'application/json';

		/**
		 * @param $data
		 * @return string
		 */
		public function convert($data){
			$pretty = $this->getOption('pretty', false);

			if( ($data = @json_encode($data,$pretty?JSON_PRETTY_PRINT:0)) === false){
				throw new \LogicException(json_last_error_msg());
			}
			return $data;
		}

	}
}

