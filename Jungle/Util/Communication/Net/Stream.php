<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 16.10.2016
 * Time: 20:35
 */
namespace Jungle\Util\Communication\Net {

	use Jungle\Util\Communication\Net\ConnectionInterface;
	use Jungle\Util\Communication\Stream\StreamInteraction;
	use Jungle\Util\Communication\Stream\StreamInteractionInterface;
	use Jungle\Util\PropContainerOptionTrait;

	/**
	 * Class Stream
	 * @package Jungle\Util\Communication\Net
	 */
	class Stream extends StreamInteraction implements ConnectionInterface, StreamInteractionInterface{

		use PropContainerOptionTrait;

		/** @var  null|string */
		protected $slug;

		/** @var  ConnectorInterface */
		protected $connector;

		/** @var  resource */
		protected $resource;

		/** @var  string */
		protected $host;

		/** @var  int */
		protected $port;

		/**
		 * Stream constructor.
		 * @param $host
		 * @param $port
		 * @param ConnectorInterface $connector
		 */
		public function __construct($host, $port, ConnectorInterface $connector = null){
			$this->host         = $host;
			$this->port         = $port;
			$this->connector    = $connector?:new TcpConnector();
		}

		/**
		 * @param $host
		 * @param $port
		 * @param ConnectorInterface|null $connector
		 * @return $this
		 * @throws \Exception
		 */
		public function reinitialize($host, $port, ConnectorInterface $connector = null){
			if($this->resource !== null){
				throw new \Exception('Reinitialize error: This stream already is liaises with "'."{$this->host}:{$this->port}".'" (please close before)');
			}
			$this->host         = $host;
			$this->port         = $port;
			$this->connector    = $connector?:new TcpConnector();
			$this->slug           = null;
			return $this;
		}

		/**
		 * @param resource $resource
		 * @return $this
		 */
		public function setResource($resource){
			if(!is_resource($resource)){
				throw new \InvalidArgumentException('passed $resource is not a resource');
			}
			$this->resource = $resource;
			return $this;
		}

		/**
		 * @return resource
		 */
		public function getResource(){
			return $this->resource;
		}

		/**
		 * @return $this
		 */
		public function connect(){
			if($this->resource === null){
				$this->connector->open($this->host, $this->port, $this);
			}
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isConnected(){
			return $this->resource!==null;
		}

		/**
		 * @return $this
		 */
		public function close(){
			if($this->resource !== null){
				$this->connector->close($this->resource);
				$this->resource = null;
				$this->_srv_options = [];
			}
			return $this;
		}

		/**
		 * @return $this
		 */
		public function reconnect(){
			if($this->resource !== null){
				$this->connector->close($this->resource);
				$this->connector->open($this->host, $this->port, $this);
			}
			return $this;
		}

		/**
		 * @return string
		 */
		public function slug(){
			if($this->slug === null){
				$this->slug = $this->connector->slug($this->host, $this->port);
			}
			return $this->slug;
		}

	}
}

