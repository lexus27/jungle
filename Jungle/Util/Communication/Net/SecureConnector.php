<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 16.10.2016
 * Time: 18:25
 */
namespace Jungle\Util\Communication\Net {

	/**
	 * Class SecureConnector
	 * @package Jungle\Util\Communication\Net
	 */
	class SecureConnector implements ConnectorInterface{

		/**
		 * SecureConnector constructor.
		 * @param ConnectorInterface $connector
		 * @param array $context
		 */
		public function __construct(ConnectorInterface $connector,array $context = []){
			$this->connector = $connector;
			$this->encryption = Encryption::once();
			$this->context = $context;
		}

		/**
		 * @param $connector
		 * @return $this
		 */
		public function setConnector(ConnectorInterface $connector){
			$this->connector = $connector;
			return $this;
		}

		/**
		 * @param $host
		 * @param $port
		 * @param Stream $stream
		 * @return Stream
		 */
		public function open($host, $port, Stream $stream = null){
			$stream = $this->connector->open($host, $port, $stream);

			$context = $this->context + [
				'SNI_enabled' => false,
				'peer_name' => $host,
			];

			$resource = $stream->getResource();
			foreach($context as $k => $v){
				stream_context_set_option($resource,'ssl', $k,$v);
			}
			$this->encryption->enable($resource);
			return $stream;
		}

		/**
		 * @return Encryption
		 */
		public function getEncryption(){
			return $this->encryption;
		}


		/**
		 * @param resource $connection
		 * @return void
		 */
		public function close($connection){
			$this->connector->close($connection);
		}

		/**
		 * @param $host
		 * @param $port
		 * @return string
		 */
		public function slug($host, $port){
			return $this->connector->slug($host, $port);
		}

		/**
		 * @return ConnectorInterface|null
		 */
		public function getAncestors(){
			return $this->connector->getAncestors() + [$this->connector];
		}

		protected static $default;

		/**
		 * @return SecureConnector
		 */
		public static function onceDefault(){
			if(!self::$default){
				self::$default = new self(TcpConnector::onceDefault());
			}
			return self::$default;
		}

	}
}

