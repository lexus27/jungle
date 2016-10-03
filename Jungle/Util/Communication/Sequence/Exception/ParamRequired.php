<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 20:38
 */
namespace Jungle\Util\Communication\Sequence\Exception {
	
	use Jungle\Util\Communication\Sequence\Exception;

	/**
	 * Class ParamRequired
	 * @package Jungle\Util\Communication\Sequence\Exception
	 */
	class ParamRequired extends Exception{

		/** @var   */
		protected $name;


		/**
		 * ParamRequired constructor.
		 * @param string $paramName
		 * @param string $message
		 * @param int $code
		 * @param \Exception $previous
		 */
		public function __construct($paramName, $message = '', $code = 0, \Exception $previous = null){
			$this->name = $paramName;

			$message = 'Parameter "'.$paramName.'" must be specified. '.$message;

			\Exception::__construct(
				$message,
				$code,
				$previous
			);
		}


	}
}

