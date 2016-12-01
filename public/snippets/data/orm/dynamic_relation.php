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
use Jungle\Data\Record\Relation\RelationForeignDynamic;
use Jungle\Data\Record\Relation\RelationMany;
use Jungle\Data\Record\Schema\Schema;


include '_boot.php';


class MediaDynamic extends AbstractModel{

	public $referenced_schema;

	public $referenced_id;

	public $media;

	/**
	 * @param Schema $schema
	 */
	public static function initialize(Schema $schema){
		$schema->setSource('media_dynamic');

		$schema->setField(  new Field('media') );
		$schema->setField(  new Field('referenced_schema') );
		$schema->setField(  new Field('referenced_id') );

		$schema->setRelation(new RelationForeignDynamic('subject','referenced_id','id','referenced_schema','restrict','cascade'));
	}

}
class MediaDynamicSubject extends AbstractModel{


	public $title;

	/**
	 * @param Schema $schema
	 */
	public static function initialize(Schema $schema){
		$schema->setSource('media_dynamic_subject');
		$schema->setField(new Field('title'));
		$schema->setRelation(new RelationMany('medias','subject', MediaDynamic::class));
	}
}



function medias_create($titlePrefix='title'){
	$subject = new MediaDynamicSubject();
	$subject->title = uniqid($titlePrefix.'_');
	for($i = 0 ; $i < 10; $i++ ){
		$media = new MediaDynamic();
		$media->media = 'hs';
		$subject->addRelated('medias', $media);
	}
	$subject->save();
}
function medias_load(){

	/** @var MediaDynamicSubject $subject */
	foreach(MediaDynamicSubject::find() as $subject){
		$medias = $subject->getRelated('medias');
		$items = $medias->getItems();
		$count = count($items);
		echo '<p>'.$count.'</p>';
	}

}
function medias_update(){
	$subject = MediaDynamicSubject::findFirst();
	$subject->title = 'my2';
	$medias = $subject->medias;
	$items = $medias->getItems();
	foreach($items as $item){
		$item->media = 'a';
		break;
	}
	$subject->save();

}

function medias_delete(){
	/** @var MediaDynamicSubject $subject */
	foreach(MediaDynamicSubject::find() as $subject){
		$subject->delete();
	}
}


medias_update();