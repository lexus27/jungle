<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.11.2016
 * Time: 20:21
 */

namespace data_orm_test;

use Jungle\Data\Record\Field\DateTime;
use Jungle\Data\Record\Field\Field;
use Jungle\Data\Record\Schema\Schema;

include '_boot.php';


class HistoryEntry extends AbstractModel{

	public $time;

	public $description;

	/**
	 * @param Schema $schema
	 */
	public static function initialize(Schema $schema){

		$schema->setSource('history');
		$schema->setField(new DateTime('time'));
		$schema->setField(new Field('description'));
	}

}



function history_create(){
	$history = new HistoryEntry();
	$history->time = time();
	$history->description = 'as';
	$history->save();
}
function history_load(){
	$entry = HistoryEntry::find();
	$items = $entry->getItems();
	$count = count($items);
}