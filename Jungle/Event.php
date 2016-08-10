<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.03.2016
 * Time: 0:04
 */
namespace Jungle {

	use Jungle\Event\EventInterface;
	use Jungle\Event\EventProcess;
	use Jungle\Event\Exception\Handling;
	use Jungle\Event\ListenerAggregateInterface;
	use Jungle\Event\Observable;

	/**
	 * Class Event
	 * @package Jungle
	 *
	 * Event Stopping
	 *      Cancelable
	 *          прекращает вызов всех идущих по очереди обработчиков,
	 *          действие (если указано) вызвано не будет
	 *
	 *      Stop
	 *          прекращает вызов всех идущих далее по очереди обработчиков,
	 *          действие (если указано) будет вызвано (ahead of time action executing)
	 *
	 *      Stop
	 *          Останавливает пропагандирование (передачу другим источников подписчиков (chain transfer))
	 *
	 */
	class Event implements EventInterface{

		/** @var  string */
		protected $_name;

		/** @var bool  */
		protected $_stoppable = false;

		/** @var  bool  */
		protected $_cancelable = false;

		/** @var  bool  */
		protected $_collector = false;

		/** @var  bool  */
		protected $_collector_dynamic = false;

		/** @var bool  */
		protected $_data_references_allowed = false;

		/** @var  bool */
		protected $_action_change_allowed = false;

		/** @var  \stdClass[] */
		protected $_memories = [];

		/**
		 * @param $name
		 * @param bool|false $stoppable
		 * @param bool|false $cancelable
		 * @param bool|false $collector
		 * @param bool $allow_data_references
		 */
		public function __construct($name, $stoppable = false,
			$cancelable = false,
			$collector = false,
			$allow_data_references = false
		){
			$this->_name                     = $name;
			$this->_cancelable               = boolval($cancelable);
			$this->_stoppable                = boolval($stoppable);
			$this->_collector                = boolval($collector);
			$this->_data_references_allowed  = boolval($allow_data_references);
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			$this->_name = $name;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getName(){
			return $this->_name;
		}

		/**
		 * @param bool|false $isCollector
		 * @return $this
		 */
		public function setCollector($isCollector = false){
			$this->_collector = boolval($isCollector);
			return $this;
		}

		/**
		 * @param bool|false $cancelable
		 * @return $this
		 */
		public function setCancelable($cancelable = false){
			$this->_cancelable = boolval($cancelable);
			return $this;
		}

		/**
		 * @param bool|false $stoppable
		 * @return $this
		 */
		public function setStoppable($stoppable = false){
			$this->_stoppable = boolval($stoppable);
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isCancelable(){
			return $this->_cancelable;
		}

		/**
		 * @return bool
		 */
		public function isStoppable(){
			return $this->_stoppable;
		}

		/**
		 * @return bool
		 */
		public function isCollector(){
			return $this->_collector;
		}

		/**
		 * @return bool
		 */
		public function isCollectorDynamic(){
			return $this->_collector_dynamic;
		}

		/**
		 * @return bool
		 */
		public function isActionChangeAllowed(){
			return $this->_action_change_allowed;
		}

		/**
		 * @return bool
		 */
		public function isDataReferencesAllowed(){
			return $this->_data_references_allowed;
		}

		/**
		 * @param null|string $key
		 * @return \stdClass
		 */
		public function getMemory($key = null){
			if(is_object($key)){
				return $key;
			}
			if($key === null){
				return $this->getMemory('default');
			}
			if(!isset($this->_memories[$key])){
				$this->_memories[$key] = new \stdClass();
			}
			return $this->_memories[$key];
		}

		/**
		 * @param ListenerAggregateInterface $initiator
		 * @param array|null $data
		 * @param callable|null $action
		 * @return EventProcess
		 */
		public function invoke(
			ListenerAggregateInterface $initiator,
			array $data = null,
			callable $action = null
		){
			$dispatching = new EventProcess($this, $initiator, $data, $action);
			return $dispatching;
		}



	}

}

