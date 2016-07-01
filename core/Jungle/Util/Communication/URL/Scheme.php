<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 23.01.2016
 * Time: 0:00
 */
namespace Jungle\Util\Communication\URL {

	use Jungle\Util\Smart\Keyword\Keyword;

	/**
	 * Class Scheme
	 * @package Jungle\Util\Communication\URL
	 */
	class Scheme extends Keyword{

		/**
		 * @param string $description
		 * @return $this
		 */
		public function setDescription($description){
			$this->setOption('description',(string)$description);
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDescription(){
			return $this->getOption('description','');
		}

		/**
		 * @param bool|true $render
		 * @return $this
		 */
		public function setPortRender($render = true){
			$this->setOption('port_render',(bool)$render);
			return $this;
		}

		/**
		 * @return bool
		 */
		public function getPortRender(){
			return (bool)$this->getOption('port_render',true);
		}

		/**
		 * @param array $portList
		 * @return $this
		 */
		public function setPortAllowedList(array $portList=null){
			if($portList === null){
				$this->setDefaultPort();
				$this->setOption('port_list',null);
				return $this;
			}
			$this->setOption('port_list',[]);
			foreach($portList as & $port){
				$this->addPort($port);
			}
			return $this;
		}

		/**
		 * @param $port
		 * @return bool
		 */
		public function isAllowedPort($port){
			$list = $this->getOption('port_list',null);
			return $list!==null?in_array(intval($port),$this->getOption('port_list',[]),true):true;
		}

		/**
		 * @param Port $port
		 */
		public function errorWrongPort(Port $port){
			throw new \LogicException('Port('.$port->getIdentifier().') not supported in scheme "'.$this->getIdentifier().'://" ');
		}

		/**
		 * @param $port
		 * @return $this
		 */
		public function setDefaultPort($port=null){
			$manager = $this->getPool()->getManager()->getPool('PortPool');
			$this->setOption('default_port',$port!==null?$manager->get(intval($port))->getIdentifier():null);
			if($port)$this->addPort($port);
			return $this;
		}


		/**
		 * @return null|Port
		 */
		public function getDefaultPort(){
			$manager = $this->getPool()->getManager()->getPool('PortPool');
			$defaultPort = $this->getOption('default_port',null);
			return $defaultPort!==null?$manager->get(intval($defaultPort)):null;
		}

		/**
		 * @return Port[]
		 */
		public function getPorts(){
			$manager = $this->getPool()->getManager()->getPool('PortPool');
			$a = [];
			foreach($this->getOption('port_list',[]) as $port){
				$a[] = $manager->get($port);
			}
			return $a;
		}

		/**
		 * @param $port
		 * @return $this
		 */
		public function addPort($port){
			$manager = $this->getPool()->getManager()->getPool('PortPool');
			/** @var Port $port */
			$port = $manager->get($port);

			if($this->searchPort($port)===false){
				$portList = (array)$this->getOption('port_list',[]);
				$portList[] = $port->getIdentifier();
				$port->addScheme($this);
				if(count($portList)===1){
					$this->setDefaultPort($port);
				}
				$this->setOption('port_list',$portList);
			}
			return $this;
		}

		/**
		 * @param $port
		 * @return mixed
		 */
		public function searchPort($port){
			return array_search(intval($port),$this->getOption('port_list',[]),true);
		}

		/**
		 * @param $port
		 * @return $this
		 */
		public function removePort($port){
			$manager = $this->getPool()->getManager()->getPool('PortPool');
			/** @var Port $port */
			$port = $manager->get($port);
			if(($i = $this->searchPort($port))!==false){
				$portList = $this->getOption('port_list',[]);
				array_splice($portList,$i,1);
				$port->removeScheme($this);
				if(count($portList)===0){
					$this->setDefaultPort(null);
				}
				$this->setOption('port_list',$portList);
			}
			return $this;
		}

	}
}

