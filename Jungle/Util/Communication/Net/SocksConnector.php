<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 16.10.2016
 * Time: 22:00
 */
namespace Jungle\Util\Communication\Net {

	/**
	 * Class SocksConnector
	 * @package Jungle\Util\Communication\Net
	 */
	class SocksConnector implements ConnectorInterface{

		/** @var   */
		private $socks_host;

		/** @var   */
		private $socks_port;

		/** @var ConnectorInterface  */
		private $connector;

		/**
		 * SocksConnector constructor.
		 * @param $socks_host
		 * @param $socks_port
		 * @param ConnectorInterface $connector
		 */
		public function __construct(ConnectorInterface $connector, $socks_host, $socks_port){
			$this->socks_host = $socks_host;
			$this->socks_port = $socks_port;
			$this->connector = $connector;
		}

		/**
		 * @param $host
		 * @param $port
		 * @param Stream $stream
		 * @return Stream
		 * @throws \Exception
		 */
		public function open($host, $port, Stream $stream = null){
			$stream = $this->connector->open($this->socks_host, $this->socks_port, $stream);

			$stream->write(pack("C3", 0x05, 0x01, 0x00));
			$status = $stream->read(8192);
			if($status != pack("C2", 0x05, 0x00)){
				throw new \Exception(
					"SOCKS Server does not support this version and/or authentication method of SOCKS"
				);
			}

			$command = $this->packDestination($host, $port);
			$stream->write($command);
			$buffer = $stream->read(8192);
			$state = substr($buffer,0,4);
			if($state != pack("C4", 0x05, 0x00, 0x00, 0x01)){
				//Connection failed.
				throw new \Exception(
					"The SOCKS server failed to connect to the specified host and port. ( ".$host.":".$port." )"
				);
			}
			return $stream;
		}

		/**
		 * @param $host
		 * @param $port
		 * @return string
		 */
		private function packDestination($host, $port){
			$ip = @inet_pton($host);
			$data = pack('C3', 0x05, 0x01, 0x00);
			if ($ip === false) {
				$data .= pack('C2', 0x03, strlen($host)) . $host;
			} else {
				$data .= pack('C', (strpos($host, ':') === false) ? 0x01 : 0x04) . $ip;
			}
			$data .= pack('n', $port);
			return $data;
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
			return $this->connector->slug($host, $port) . "<{$this->socks_host}:{$this->socks_port}";
		}

		/**
		 * @return ConnectorInterface|null
		 */
		public function getAncestors(){
			return $this->connector->getAncestors() + [$this->connector];
		}

	}
}

