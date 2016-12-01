<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.11.2016
 * Time: 20:10
 */
namespace data_orm_test;

use Jungle\Data\Record\Field\Field;
use Jungle\Data\Record\Schema\Schema;

include '_boot.php';


class Contact extends AbstractModel{

	public $contact_type;

	public $definition;

	public static function initialize(Schema $schema){
		$schema->setField(new Field('contact_type'));
		$schema->setField(new Field('definition'));


		$schema->setBootField('contact_type');

		$schema->setSource('contact');
	}

	public function getAddress(){
		return $this->definition;
	}

}
class ContactMobilephone extends Contact{

	public static function initialize(Schema $schema){
	}

}
class ContactEmail extends Contact{

	public static function initialize(Schema $schema){
	}

}


function contact_create(){
	$contact = new ContactEmail();
	$contact->definition = 'mail@mail.ru';
	$contact->save();
}
