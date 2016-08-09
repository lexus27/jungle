<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 29.07.2016
 * Time: 6:05
 */
namespace App\Model\User\Contact {
	
	use App\Model\User\Contact;
	use Jungle\Data\Record\Head\Schema;

	/**
	 * Class Mobilephone
	 * @package App\Model\User\Contact
	 */
	class Mobilephone extends Contact{

		/**
		 * @param Schema $schema
		 */
		public static function initialize(Schema $schema){
			$schema->field('address',[
				'type' => [
					'type' => 'pattern',
				    'params' => [
					    'pattern' => '+?[\d]{11}'
				    ]
				],
			]);
		}
	}
}

