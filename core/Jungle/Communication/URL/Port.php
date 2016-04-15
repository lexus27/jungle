<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 22.01.2016
 * Time: 23:59
 */
namespace Jungle\Communication\URL {

	use Jungle\Smart\Keyword\Keyword;

	/**
	 * Class Port
	 * @package Jungle\Communication\URL
	 */
	class Port extends Keyword{

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
		 * @param array $schemeList
		 * @return $this
		 */
		public function setSchemeAllowedList(array $schemeList=null){
			if($schemeList === null){
				$this->setDefaultScheme();
				$this->setOption('scheme_list',null);
				return $this;
			}
			$this->setOption('scheme_list',[]);
			foreach($schemeList as & $scheme){
				$this->addScheme($scheme);
			}
			return $this;
		}

		/**
		 * @param $scheme
		 * @return bool
		 */
		public function isAllowedScheme($scheme){
			$list = $this->getOption('scheme_list',[]);
			return $list!==null?in_array((string)$scheme,$this->getOption('scheme_list',[]),true):true;
		}


		/**
		 * @param $scheme
		 * @return $this
		 */
		public function setDefaultScheme($scheme=null){
			$manager = $this->getPool()->getManager()->getPool('SchemePool');
			$this->setOption('default_scheme',$scheme!==null?$manager->get((string)$scheme)->getIdentifier():null);
			if($scheme){
				$this->addScheme($scheme);
			}
			return $this;
		}

		/**
		 * @return null|Scheme
		 */
		public function getDefaultScheme(){
			$manager = $this->getPool()->getManager()->getPool('SchemePool');
			$defaultScheme = $this->getOption('default_scheme',null);
			return $defaultScheme!==null? $manager->get((string)$defaultScheme) : null ;
		}

		/**
		 * @return Scheme[]
		 */
		public function getSchemes(){
			$manager = $this->getPool()->getManager()->getPool('SchemePool');
			$a = [];
			foreach($this->getOption('scheme_list',[]) as $scheme){
				$a[] = $manager->get($scheme);
			}
			return $a;
		}

		/**
		 * @param $scheme
		 * @return $this
		 */
		public function addScheme($scheme){
			$manager = $this->getPool()->getManager()->getPool('SchemePool');
			/** @var Scheme $scheme */
			$scheme = $manager->get($scheme);

			if($this->searchScheme($scheme)===false){
				$schemeList = (array)$this->getOption('scheme_list',[]);
				$schemeList[] = $scheme->getIdentifier();
				$scheme->addPort($this);
				if(count($schemeList)===1){
					$this->setDefaultScheme($scheme);
				}
				$this->setOption('scheme_list',$schemeList);
			}
			return $this;
		}

		/**
		 * @param $scheme
		 * @return mixed
		 */
		public function searchScheme($scheme){
			return array_search((string)$scheme,$this->getOption('scheme_list',[]),true);
		}

		/**
		 * @param $scheme
		 * @return $this
		 */
		public function removeScheme($scheme){
			$manager = $this->getPool()->getManager()->getPool('SchemePool');
			/** @var Scheme $scheme */
			$scheme = $manager->get($scheme);
			if(($i = $this->searchScheme($scheme))!==false){
				$schemeList = $this->getOption('scheme_list',[]);
				array_splice($schemeList,$i,1);
				$scheme->removePort($this);
				if(count($schemeList)===0){
					$this->setDefaultScheme(null);
				}
				$this->setOption('scheme_list',$schemeList);
			}
			return $this;
		}

		/**
		 * @param string $identifier
		 */
		public function setIdentifier($identifier){
			if(!$identifier){
				throw new \LogicException('Port identifier must be numeric a port number');
			}
			parent::setIdentifier(intval($identifier));
		}

		/**
		 * @param string $identifier1
		 * @param string $identifier2
		 * @param callable|null $func (strcasecmp or strcmp and them)
		 * @return bool
		 */
		public function compareIdentifiers($identifier1,$identifier2,callable $func=null){
			return $func?(bool)call_user_func($func,$identifier1,$identifier2):intval($identifier1) === intval($identifier2);
		}


	}
}

