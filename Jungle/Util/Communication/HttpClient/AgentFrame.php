<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.10.2016
 * Time: 23:37
 */
namespace Jungle\Util\Communication\HttpClient {

	use Jungle\Util\Communication\Net\ConnectorInterface;

	/**
	 * Class AgentFrame
	 * @package Jungle\Util\Communication\HttpClient
	 */
	class AgentFrame extends AgentDecorator{

		/**
		 * AgentFrame constructor.
		 * @param Agent $agent
		 * @param ConnectorInterface $connector
		 */
		public function __construct(Agent $agent, ConnectorInterface $connector){
			$this->connector    = $connector;
			$this->agent        = $agent;
		}

		/**
		 * @return ConnectorInterface
		 */
		protected function getConnector(){
			return $this->connector;
		}

		/**
		 * @return \Jungle\Util\Communication\Net\SecureConnector
		 */
		protected function getSecureConnector(){
			return parent::getSecureConnector();
		}


	}
}

