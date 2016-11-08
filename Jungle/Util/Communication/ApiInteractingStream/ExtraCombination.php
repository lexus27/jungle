<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 21.10.2016
 * Time: 14:18
 */
namespace Jungle\Util\Communication\ApiInteractingStream {

	use Jungle\Util\Communication\Net\ConnectionInterface;
	use Jungle\Util\Communication\Net\ConnectorInterface;
	use Jungle\Util\Communication\Net\SecureConnector;
	use Jungle\Util\Communication\Net\Stream;
	use Jungle\Util\Communication\Net\TcpConnector;
	use Jungle\Util\Communication\Stream\StreamInteractionInterface;

	/**
	 * Class ExtraCombination
	 * @package Jungle\Util\Communication\ApiInteractingStream
	 */
	class ExtraCombination extends Combination{

		/** @var Api  */
		protected $api;

		/**
		 * ExtraCombination constructor.
		 * @param Api $api
		 * @param $connector
		 * @param $secure_connector
		 */
		public function __construct(Api $api, ConnectorInterface $connector = null, SecureConnector $secure_connector = null){
			$this->api = $api;
			$this->default_params = [
				'host'              => null,
				'port'              => null,
				'secure'            => false,
				'connector'         => $connector,
				'secure_connector'  => $secure_connector,
			];
		}

		/**
		 * @return TcpConnector
		 */
		protected function getConnector(){
			return $this->default_params['connector']?:TcpConnector::onceDefault();
		}

		/**
		 * @return SecureConnector
		 */
		protected function getSecureConnector(){
			return $this->default_params['secure_connector']?:SecureConnector::onceDefault();
		}

		/**
		 * @return StreamInteractionInterface
		 */
		public function getStream(){
			if(!$this->stream){
				$this->stream = new Stream(
					$this->default_params['host'],
					$this->default_params['port'],
					$this->default_params['secure']?
						$this->getSecureConnector():
						$this->getConnector()
				);
			}
			return $this->stream;
		}


		/**
		 * @param array $config
		 * @return \Jungle\Util\Communication\ApiInteracting\Collector
		 */
		public function run(array $config = []){
			$this->setDefaultParams($config, true);
			$stream = $this->getStream();
			if($stream instanceof ConnectionInterface){
				$stream->close();
			}

			if($stream instanceof Stream){
				$stream->reinitialize(
					$this->default_params['host'],
					$this->default_params['port'],
					$this->default_params['secure']?
						$this->getSecureConnector():
						$this->getConnector()
				);
			}

			return $this->api->interact($this);
		}


	}
}

