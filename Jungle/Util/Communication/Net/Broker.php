<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 3:11
 */
namespace Jungle\Util\Communication\Net {

	/**
	 * Class Broker
	 * @package Jungle\Util\Communication\Net
	 */
	class Broker implements BrokerInterface{

		/** @var array[]  */
		protected static $closed = [];

		/** @var array[]  */
		protected $idle     = [];

		/** @var array[]  */
		protected $pending  = [];

		/** @var bool  */
		protected $terminating = false;

		/**
		 * @param $host
		 * @param $port
		 * @param ConnectorInterface $connector
		 * @return Stream
		 */
		public function take($host, $port, ConnectorInterface $connector = null){
			$this->terminating = false;
			if(!$connector){
				$connector = TcpConnector::onceDefault();
			}
			$slug = $connector->slug($host, $port);

			// shift idle
			$stream = null;
			if(isset($this->idle[$slug])){
				$stream = array_shift($this->idle[$slug]);
			}

			if(!$stream){
				if(($stream = array_shift(self::$closed))){
					$stream->reinitialize($host, $port, $connector);
					$stream->connect();
				}else{
					$stream = $connector->open($host,$port);
				}
			}
			if(!isset($this->pending[$slug])){
				$this->pending[$slug] = [];
			}
			$this->pending[$slug][] = $stream;
			return $stream;
		}


		/**
		 * @param Stream $stream
		 */
		public function pass(Stream $stream){
			$slug = $stream->slug();
			if($stream->isConnected()){

				if(!$this->terminating){
					//add idle
					if(!isset($this->idle[$slug])){
						$this->idle[$slug] = [];
					}
					$this->idle[$slug][] = $stream;
				}else{
					$stream->close();
					self::$closed[] = $stream;
				}

				// remove pending
				if(isset($this->pending[$slug])){
					$i = array_search($stream, $this->pending[$slug], true);
					if($i !== false){
						array_splice($this->pending[$slug], $i, 1);
						if(!$this->pending[$slug]){
							unset($this->pending[$slug]);
						}
					}
				}

			}else{
				self::$closed[] = $stream;
			}
		}


		/**
		 * @return $this
		 */
		public function terminate(){
			$this->terminating = true;
			foreach($this->idle as $key => $idles){
				/** @var ConnectionInterface $idle */
				foreach($idles as $idle){
					$idle->close();
					Broker::$closed[] = $idle;
				}

			}
			return $this;
		}

	}
}

