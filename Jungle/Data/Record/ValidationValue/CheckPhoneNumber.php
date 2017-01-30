<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.11.2016
 * Time: 15:21
 */
namespace Jungle\Data\Record\ValidationValue {
	
	use Jungle\Data\Record;

	/**
	 * Class CheckPhoneNumber
	 * @package Jungle\Data\Record\Validator
	 */
	class CheckPhoneNumber extends CheckPattern{

		/**
		 * CheckPhoneNumber constructor.
		 * @param null $pattern
		 * @param array|null $min_max
		 */
		public function __construct($pattern = null, array $min_max = null){
			if(!$pattern){
				if(!is_array($min_max)){
					$min_max = [11];
				}
				$pattern = '@^\+?[\d]{'.implode(',',$min_max).'}$@';
			}
			$pattern = $pattern?: '@+?[\d]{11}@';
			parent::__construct($pattern);
		}
	}
}

