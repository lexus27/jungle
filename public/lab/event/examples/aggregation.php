<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.04.2016
 * Time: 16:15
 */


namespace event;

use Jungle\Event\EventProcess;
use Jungle\Event\Listener\ListenerProcess;
use Jungle\Event\Listener\Subscriber;
use Jungle\Event\Observable\ObservableTrait;
use Jungle\Event\ObservableInterface;

require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'loader.php';


class Source implements ObservableInterface{
	use ObservableTrait;

	protected $name;

	public function __construct($name){
		$this->name = $name;
	}

	public function getName(){
		return $this->name;
	}

	/**
	 * @return void|array
	 */
	protected static function __define_events(){
		return [
			'beforeLoad' => [
				'stoppable' => false,
				'cancelable' => true
			],
			'load'
		];
	}

	public function load(){
		if($this->invokeEvent('beforeLoad',null)!==false){

			$data = [
				'count'     => 1,
				'records'   => [
					[1,'N','KHV']
				]
			];

			$this->invokeEvent('load',$data);

		}
	}


}

class Collection implements ObservableInterface{
	use ObservableTrait;

	/** @var  Source */
	protected $_source;

	public function __construct(){
		$this->registerListenerBranch('source',[
			'prefix'            => true,
			'aggregate'         => true,
			'aggregate_prefix'  => true,
		]);
	}

	/**
	 * @param Source $source
	 * @return $this
	 */
	public function setSource(Source $source){
		$old = $this->_source;
		if($old !== $source){
			$this->_source = $source;
			if($old){
				$this->detachListenerBranchFrom('source',$old);
			}
			if($source){
				$this->attachListenerBranchTo('source',$source);
			}
		}
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSource(){
		return $this->_source;
	}

	public function sourceBeforeLoad(ListenerProcess $lp,EventProcess $ep){
		echo '<p/>'.get_class($this) . ' ' . $lp->getEvent()->getName();
	}

}

/**
 * Class View
 * @package event
 */
class View implements ObservableInterface{
	use ObservableTrait;

	/** @var  Collection */
	protected $_collection;

	public function __construct(){
		$this->registerListenerBranch('collection',[
			'prefix'            => true,
			'aggregate'         => true,
			'aggregate_prefix'  => true,
		]);
	}

	/**
	 * @param Collection $collection
	 * @return $this
	 */
	public function setCollection(Collection $collection){
		$old = $this->_collection;
		if($old !== $collection){
			$this->_collection = $collection;
			if($old){
				$this->detachListenerBranchFrom('collection',$old);
			}
			if($collection){
				$this->attachListenerBranchTo('collection',$collection);
			}
		}
		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getCollection(){
		return $this->_collection;
	}

	/**
	 * @param ListenerProcess $lp
	 * @param EventProcess $ep
	 */
	public function collectionBeforeLoad(ListenerProcess $lp,EventProcess $ep){
		echo '<p/>'.get_class($this) . ' ' . $lp->getEvent()->getName();
	}

}

$source1     = new Source('source_1');
$source2     = new Source('source_2');
$collection = new Collection();
$view       = new View();

$view->setCollection($collection);

$source1->attachEventListener(new Subscriber('beforeLoad',function(ListenerProcess $lp,EventProcess $ep){
	echo '<p/>Source 1 Subscriber '. $lp->getEvent()->getName(). ' : '. $ep->getEvent()->getName();
}));
$source2->attachEventListener(new Subscriber('beforeLoad',function(ListenerProcess $lp,EventProcess $ep){
	echo '<p/>Source 2 Subscriber '. $lp->getEvent()->getName(). ' : '. $ep->getEvent()->getName();
}));

$collection->attachEventListener(new Subscriber('source.beforeLoad',function(ListenerProcess $lp,EventProcess $ep){
	echo '<p/>Collection Subscriber src name: '. $ep->getInitiator()->getName() . ' ' . $lp->getEvent()->getName();
}));
$view->attachEventListener(new Subscriber('collection.source.beforeLoad',function(ListenerProcess $lp,EventProcess $ep){

	/**
	 * Агрегатор, это объект-перехватчик события от инициатора, тот объект к которому был приатачен слушатель
	 * @var View $aggregate
	 *
	 * Инициатор события
	 * @var Source $initiator
	 */
	$aggregate = $lp->getAggregate();
	$initiator = $ep->getInitiator();
	echo '<p/>View Subscriber src name: "'. $initiator->getName() . '" aggregator: "'.get_class($aggregate) . '" '
	     .$lp->getEvent()->getName();
}));

/**
 * Слушатели -- слушают
 * В коллекции вдруг появляется источник
 */
echo '<h1>Source 1</h1>';
$collection->setSource($source1);
/** Загрузка */
$collection->getSource()->load();

/**
 *
 * В коллекции по ходу работы кода меняется источник
 *
 */
echo '<h1>Source 2</h1>';
$collection->setSource($source2);
/** Загрузка */
$collection->getSource()->load();


/**
 * Заметка: для приатаченых слушателей к объектам view and collection мы ничего не изменяли
 * Но в коллекции менялся $source
 */

//$source2->load();
echo '<pre>';
//print_r($view);

