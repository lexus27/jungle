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
	 * Class Email
	 * @package App\Model\User\Contact
	 */
	class Email extends Contact{


		public static function initialize(Schema $schema){
			$schema->field('address',[
				'type' => 'email'
			]);
		}

	}
}

