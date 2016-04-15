<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 06.01.2016
 * Time: 20:15
 */
namespace Jungle\Communication\URL\Host {

	use Jungle\Communication\URL\Host;

	/**
	 * Class Domain
	 * @package Jungle\Communication\URL\Host
	 */
	class Domain{

		/** @var Domain */
		protected $parent;

		/** @var Domain[] */
		protected $children = [];

		/** @var string */
		protected $name;

		/** @var Domain[] */
		protected static $domains = [];

		/**
		 * @param $name
		 * @return Domain
		 */
		public function setName($name){
			return $this->decomposite($name);
		}

		/**
		 * @param $domain
		 * @return $this|null
		 */
		public function decomposite($domain){
			if(!is_array($domain))$domain = explode('.',$domain);
			$name = array_pop($domain);
			if($this->name!==$name){
				$this->name = $name;
			}
			if($domain){
				/** @var Domain $base */
				$contain = $this->getContain($domain,$base, $domainChunks);
				if($contain){
					return $contain;
				}
				$d = new Domain();
				$target = $d->decomposite($domainChunks);
				$base->addChild($d);
				return $target;
			}else{
				return $this;
			}

		}

		/**
		 * @param $domain
		 * @param Domain $lastBase
		 * @param $d
		 * @return null
		 */
		public function getContain($domain,Domain & $lastBase = null, & $d = null){
			if(!is_array($domain))$domain = explode('.',$domain);
			$name = array_pop($domain);
			foreach($this->children as $child){
				if(strcasecmp($child->getName() , $name)===0){
					if(!$domain){
						$d          = [];
						$lastBase   = $child;
						return $child;
					}else{
						return $child->getContain($domain,$lastBase,$d);
					}
				}
			}
			$d          = $domain;
			$d[]        = $name;
			$lastBase   = $this;
			return null;
		}

		/**
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param Domain|null $parent
		 * @param bool|false $appliedInNew
		 * @param bool|false $appliedInOld
		 * @return $this
		 */
		public function setParent(Domain $parent = null, $appliedInNew = false, $appliedInOld = false){
			$old = $this->parent;
			if($old !== $parent){
				$this->parent = $parent;
				if(!$appliedInOld && $old){
					$old->removeChild($this);
				}
				if(!$appliedInNew && $parent){
					$parent->addChild($this);
				}
			}
			return $this;
		}

		/**
		 * @return Domain
		 */
		public function getParent(){
			return $this->parent;
		}

		/**
		 * @param Domain $domain
		 * @return bool
		 */
		public function isContains(Domain $domain){
			if($this->searchChild($domain)!==false){
				return true;
			}else{
				foreach($this->children as $d){
					if($d->isContains($domain)){
						return true;
					}
				}
			}
			return false;
		}

		/**
		 * @param $domainName
		 * @return bool
		 */
		public function isEntityContains($domainName){
			if($this->hasChild($domainName)){
				return true;
			}else{
				foreach($this->children as $d){
					if($d->isEntityContains($domainName)){
						return true;
					}
				}
			}
			return false;
		}

		/**
		 * @param $domainName
		 * @return bool
		 */
		public function hasChild($domainName){
			foreach($this->children as $domain){
				if(strcasecmp($domainName,$domain->getName())===0){
					return true;
				}
			}
			return false;
		}

		/**
		 * @param $domainName
		 * @return Domain|null
		 */
		public function getChild($domainName){
			foreach($this->children as $domain){
				if(strcasecmp($domainName,$domain->getName())===0){
					return $domain;
				}
			}
			return null;
		}

		/**
		 * @param Domain $domain
		 * @param bool $appliedInParent
		 * @return $this
		 */
		public function addChild(Domain $domain,$appliedInParent = false){
			if($domain->isContains($this)){
				throw new \LogicException('Domain "'.$domain.'" is parent for "'.$this.'" domain');
			}
			if($this->searchChild($domain)===false){
				$this->children[] = $domain;
				if(!$appliedInParent){
					$domain->setParent($this,true);
				}
			}
			return $this;
		}

		/**
		 * @param Domain $domain
		 * @return mixed
		 */
		public function searchChild(Domain $domain){
			return array_search($domain,$this->children,true);
		}

		/**
		 * @param Domain $domain
		 * @param bool $appliedInParent
		 * @return $this
		 */
		public function removeChild(Domain $domain,$appliedInParent = false){
			if(($i = $this->searchChild($domain)) !==false){
				array_splice($this->children,$i,1);
				if(!$appliedInParent){
					$domain->setParent(null,true,true);
				}
			}
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isHost(){
			return $this->parent && $this->parent->isZone();
		}

		/**
		 * @return bool|Domain
		 */
		public function getHost(){
			if($this->isZone()){
				return false;
			}
			$domain = $this;
			while($domain->parent){
				if($domain->isHost()){
					return $domain;
				}
				$domain = $domain->parent;
			}
			return false;
		}

		/**
		 * @return bool
		 */
		public function isZone(){
			return !$this->parent;
		}

		/**
		 * @return Domain
		 */
		public function getZone(){
			$domain = $this;
			while($domain->parent){
				$domain = $domain->parent;
			}
			return $domain;
		}

		/**
		 * @return bool
		 */
		public function isSubDomain(){
			return $this->parent && !$this->parent->isZone();
		}

		/**
		 * @param int $upOn
		 * @return Domain
		 */
		public function getLevelUp($upOn=1){
			$domain = $this;
			while($upOn-- && $domain){
				$domain = $domain->parent;
			}
			return $domain;
		}

		/**
		 * @return int
		 */
		public function getGlobalLevel(){
			$level = 1;
			$parent = $this->parent;
			while($parent){
				$level++;
				$parent = $parent->parent;
			}
			return $level;
		}

		/**
		 * @return int|false
		 */
		public function getSubLevel(){
			$level = $this->getGlobalLevel();
			if($level > 2){
				return $level - 2;
			}
			return false;
		}

		/**
		 * @return int
		 */
		public function getNeighborsCount(){
			return $this->parent?$this->parent->count()-1:0;
		}

		/**
		 * @param $name
		 * @return Domain|null
		 */
		public function getNeighbor($name){
			if($this->parent){
				return $this->parent->getChild($name);
			}
			return null;
		}

		/**
		 * @return int
		 */
		public function count(){
			return count($this->children);
		}


		/**
		 * @return mixed
		 */
		public function getIP(){
			return IP::get(gethostbyname($this->__toString()));
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return $this->name  . ($this->parent?('.' .$this->parent):'');
		}

		/**
		 * @param $domain
		 * @return int
		 */
		public static function match($domain){
			return (bool) preg_match('@[\w]+\.[\w]{2,}]@',trim(trim($domain),'/'));
		}

		/**
		 * @param $domain
		 * @return Domain|null
		 */
		public static function get($domain){
			if(IP::isIPAddress($domain)){
				$d = gethostbyaddr($domain);
				if($d){
					$domain = $d;
				}
			}
			$domain = strtolower($domain);
			$domain = explode('.',$domain);
			$parent = null;
			foreach(self::$domains as $d){
				if(strcasecmp($d,$domain[count($domain)-1])===0){
					$parent = $d;
				}
			}
			if($parent){
				return $parent->decomposite($domain);
			}else{
				$d = new Domain();
				self::$domains[] = $d;
				return $d->decomposite($domain);
			}
		}

		/**
		 * @param $address
		 * @return string
		 */
		public static function isDomain($address){
			return gethostbyname($address);
		}

	}
}

