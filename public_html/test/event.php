<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.03.2016
 * Time: 18:47
 */
$e = '';
/**
 * @param $value
 */
function view($value){
	$GLOBALS['e'].= '<p>'.$value.'</p>';
}

/**
 * @param $value
 */
function alert($value){
	$GLOBALS['e'].= '<script>alert("'.addcslashes($value,'"').'");</script>';
}

/**
 * @param $title
 * @param $value
 */
function block($title, $value){
	ob_start();var_dump($value);$r = ob_get_clean();
	$GLOBALS['e'].= '<div><p>'.$title.'</p><pre>'.$r.'</pre></div>';
}
require __DIR__ . DIRECTORY_SEPARATOR . 'loader.php';

use Jungle\Event;
use Jungle\Event\EventProcess;
use Jungle\Event\Listener;
use Jungle\Event\Listener\ListenerProcess;

\Jungle\Jungle::startTime('prepare');
\Jungle\Jungle::startMemoryMeasure('prepare');


class Source_ implements Event\ObservableInterface{
	use Event\Observable\ObservableTrait;


	protected static function __define_events(){
		return [
			'beforeLoad' => [
				'cancelable'        => true,
				'stoppable'         => true,
				'data_references'   => true,
				'collector'         => true,
				'collector_dynamic' => true
			],

			'load'

		];
	}


	public function load(){
		$this->invokeEvent('beforeLoad',null,function(EventProcess $e){
			view('Действие '. $e->getEvent()->getName());

			$collected = $e->getCollected();

			$this->invokeEvent('load',['records' => [], 'count'   => 0],function(EventProcess $e){
				view('Действие 2 ' . $e->getEvent()->getName());
			});
		});
	}

}
class Collection_ implements Event\ObservableInterface{

	use Event\Observable\ObservableTrait;

	protected $source;

	/**
	 *
	 */
	public function __construct(){
		$this->registerListenerBranch('source',[
			'prefix'            => true,
			'aggregate'         => true,
			'aggregate_prefix'  => true,
		]);
	}

	/**
	 * @param Source_ $src
	 */
	public function setSource(Source_ $src){
		$old = $this->source;
		if($old !== $src){
			$this->source = $src;
			if($old){
				$this->detachListenerBranchFrom('source',$old);
			}
			if($src){
				$this->attachListenerBranchTo('source',$src);
			}
		}
	}

	/**
	 * @param EventProcess $d
	 * @param ListenerProcess $l
	 */
	public function sourceBeforeLoad(ListenerProcess $l, EventProcess $d){
		//$d->cancel();
	}

}
$source = new Source_();
for($i = 0 ; $i < 1; $i++){
	$source->attachEventListener((new Listener\Subscriber('beforeLoad',
		function($string, ListenerProcess $listener, EventProcess $dispatching){
			view('Event: '.$listener->getEvent()->getName());
			$dispatching->data++;
			$dispatching->send('Sent value!');
			block('Data',$dispatching->getData());
		},
		10,1,false, ['Hello'])));
}

$collection = new Collection_();
$collection->setSource($source);
for($i = 0 ; $i < 1; $i++){
	$collection->attachEventListener((new Listener\Subscriber('source.BeforeLoad',function(
		ListenerProcess $listener, EventProcess $dispatching){
		view('Event: '.$listener->getEvent()->getName());
		$dispatching->addAdditionalAction(function(){view('Дополнительное действие');});
	})));
}

$collection->attachEventListener((new Listener\Subscriber('source.Load',function($listener, EventProcess $eventProcess){
	view('Event: '.$eventProcess->getEvent()->getName());
})));

view(sprintf('Prepared with: %.4F sec',\Jungle\Jungle::endTime('prepare')));
view(sprintf('Prepare Memory Usage: %.4F mb',\Jungle\Jungle::endMemoryMeasure('prepare')));
\Jungle\Jungle::startTime('execute');
\Jungle\Jungle::startMemoryMeasure('execute');


$source->load();

//block('Source', $source);
view(sprintf('Executed with: %.4F sec',\Jungle\Jungle::endTime('execute')));
view(sprintf('Memory Usage: %.4F mb',\Jungle\Jungle::endMemoryMeasure('execute')));


echo $e;