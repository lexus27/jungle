<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.11.2016
 * Time: 20:09
 */

namespace data_orm_test;

use Jungle\Data\Record\Field\DateTime;
use Jungle\Data\Record\Field\Field;
use Jungle\Data\Record\Schema\Schema;

include '_boot.php';


class Note extends AbstractModel{

	public $title;

	public $content;

	public $created_time;

	public $update_time;

	public static function initialize(Schema $schema){
		//$schema->mixin('id_base'); idea for implements SchemaFragment class as Mixins for general schemas

		$schema->setField(new Field('title'));
		$schema->setField(new Field('content'));
		$schema->setField(new DateTime('created_time'));
		$schema->setField(new DateTime('update_time'));

		// выставляем source напрямую в схему - Это важнее чем через метод
		$schema->setSource('notes');
	}

	/**
	 * @return string
	 */
	public function getSource(){
		// выставляем source через этот метод
		return 'notes';
	}


	public function beforeCreate(){
		$this->created_time = time();
		$this->update_time = $this->created_time;
	}

	public function beforeUpdate(){
		$this->update_time = time();
	}

}

function note_create(){
	$note = new Note();

	$note->title = uniqid('test');
	$note->content = 'vasya';
	$note->save();
}