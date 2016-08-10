<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.03.2016
 * Time: 15:16
 */

use Jungle\Event;
use Jungle\Event\Listener\Observer;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'loader.php';


class Source implements Event\ListenerAggregateInterface{

	use Event\Observable\ObservableTrait;

	public function __construct(){

	}

	public function load(){

		$records = [];
		for($i = 0; $i < 200000; $i++){
			$records[] = [$i, 'Retina display '.$i];
		}
		$count = count($records);
		$this->invokeEvent([
			'name'              => 'load',
			'data_references'   => true
		],[

			'count'     => & $count,

			'records'   => & $records

		],function(){

			//echo 'Действие выполнено';

		});


		echo '<p>Count: '.$count.'</p>';

	}

}



class Collection implements Event\ListenerAggregateInterface{

	use Event\Observable\ObservableTrait;

	protected $source_observer;
	protected $source;

	public function __construct(){
		$this->source_observer = new Observer($this,'source');
		$this->source_observer->setAggregate($this,true);
	}

	public function setSource(Source $source){
		$this->source = $source;
		$this->source->attachEventListener($this->source_observer);
	}

	public function sourceLoad(Event $event){

		echo '<p>Source Loaded</p>';


	}

}
class CollectionView implements Event\ListenerAggregateInterface{

	use Event\Observable\ObservableTrait;

	protected $collection_observer;

	protected $collection;

	public function __construct(){
		$this->collection_observer = new Observer($this,'collection');
		$this->collection_observer->setAggregate($this);
	}


	public function setCollection(Collection $collection){
		$this->collection = $collection;
		$this->collection->attachEventListener($this->collection_observer);
	}


	public function collectionLoad(Event $event){

		echo '<p>Source Loaded (collection view)</p>';


	}

}
\Jungle\Jungle::startTime('test');
$source = new Source();

$collection = new Collection();
$collection->setSource($source);

$view = new CollectionView();
$view->setCollection($collection);

$view->attachEventListener( (new Event\Listener\Subscriber(function(
		Event\EventProcess $ep,
		Event\Listener\ListenerProcess $lp
){

	//echo '<p>[',implode(', ',['`id`','`name`']),']</p>';
	foreach($ep->records as $record){
		//echo '<p>[',implode(', ',array_map(function($v){return var_export($v,true);},$record)),']</p>';
	}
},'load')) );


$source->load();
echo \Jungle\Jungle::endTime('test','<p>%.4F</p>');

