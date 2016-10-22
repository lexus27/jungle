<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 16.10.2016
 * Time: 20:22
 */
namespace Jungle\Util\Communication\Net {

	/**
	 * Class TcpConnector
	 * @package Jungle\Util\Communication\Net
	 */
	class TcpConnector implements ConnectorInterface{

		/** @var  array */
		protected $context;

		/**
		 * TcpConnector constructor.
		 * @param $context
		 */
		public function __construct(array $context = []){
			$this->context = $context;
		}

		/**
		 * @param $host
		 * @param $port
		 * @param Stream $stream
		 * @return Stream
		 * @throws \Exception
		 */
		public function open($host, $port, Stream $stream = null){
			$url = $this->resolveAddress($host, $port);
			$fp = @stream_socket_client(
				$url, $error_number, $error_string, 2,
				STREAM_CLIENT_CONNECT,
				stream_context_create([
					'socket' => $this->context
				])
			);
			if(!is_resource($fp)){
				throw new \Exception(sprintf("Connection to %s:%d failed: [%d]%s", $host,$port, $error_number, $this->_normalizeErrorString($error_string)));
			}
			stream_set_blocking($fp, true);
			$name = stream_socket_get_name($fp, true);
			if ($name === false) {
				fclose($fp);
				throw new \Exception('Connection refused');
			}
			$stream = $stream?: new Stream($host, $port, $this);
			$stream->setResource($fp);
			return $stream;
		}

		/**
		 * @param $error_string
		 * @return string
		 */
		protected function _normalizeErrorString($error_string){
			return mb_convert_encoding($error_string, 'utf-8','cp1251');
		}

		/**
		 * @param $host
		 * @param $port
		 * @return string
		 */
		protected function resolveAddress($host, $port){
			if(strpos($host,':')!==false){
				$host = "[{$host}]";
			}
			return "{$host}:{$port}";

		}

		/**
		 * @param resource $connection
		 * @return void
		 */
		public function close($connection){
			fclose($connection);
		}

		/**
		 * @param $host
		 * @param $port
		 * @return string
		 */
		public function slug($host, $port){
			return "{$host}:{$port}";
		}

		/**
		 * @return ConnectorInterface[]
		 */
		public function getAncestors(){
			return [];
		}

		/**
		 * @return TcpConnector
		 */
		public static function onceDefault(){
			static $instance;
			if(!$instance){
				$instance = new self();
			}
			return $instance;
		}

	}
}

