<?php

namespace Jungle\XPlate\CSS {

	use Jungle\Basic\INamedBase;
	use Jungle\XPlate\Interfaces\IWebStrategy;
	use Jungle\XPlate\Services\AttributeManager;
	use Jungle\XPlate\Services\StyleManager;
	use Jungle\XPlate\Services\TagManager;

	/**
	 * Class Document
	 * @package Jungle\XPlate\CSS
	 *
	 * Документ
	 *
	 */
	class Document extends RuleSpace implements INamedBase{

		protected static $pretty_mode = false;

		protected static $pretty_indent = "\t";

		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var IWebStrategy
		 */
		protected $strategy;

		/**
		 * @var Animation[]
		 */
		protected $animations = [];

		/**
		 * @var Media[]
		 */
		protected $medias = [];





		/**
		 * @param IWebStrategy $strategy
		 */
		public function __construct(IWebStrategy $strategy){
			$this->setStrategy($strategy);
		}

		/**
		 * @param IWebStrategy $strategy
		 * @param bool $appliedInStrategy
		 * @param bool $appliedInOld
		 * @return mixed
		 */
		public function setStrategy(IWebStrategy $strategy, $appliedInStrategy = false, $appliedInOld = false){
			$old = $this->strategy;
			if($old!==$strategy){
				$this->strategy = $strategy;
				if($strategy && !$appliedInStrategy){
					$strategy->addDocument($this,true);
				}
				if($old && !$appliedInOld){
					$old->removeDocument($this,true);
				}
			}
			return $this;
		}

		/**
		 * @return IWebStrategy
		 */
		public function getStrategy(){
			return $this->strategy;
		}

		/**
		 * Выставить имя объекту
		 * @param string $name
		 * @return $this
		 */
		public function setName($name){
			if(!$name){
				throw new \LogicException('Name is empty');
			}
			$this->name = $name;
			return $this;
		}

		/**
		 * Получить имя объекта
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param Media $media
		 * @return $this
		 */
		public function addMedia(Media $media){
			if(!$media->getName()){
				throw new \LogicException('Document.addMedia($Media), Media is not named');
			}
			if(!$this->getMedia($media)){
				$this->medias[] = $media;
			}
			return $this;
		}

		/**
		 * @param Media $media
		 * @return mixed
		 */
		public function searchMedia(Media $media){
			return array_search($media,$this->medias,true);
		}

		/**
		 * @param $media
		 * @return Media|null
		 */
		public function getMedia($media){
			if($media instanceof Media){
				$media = $media->getName();
			}
			foreach($this->medias as $m){
				if($m->getName() === $media){
					return $m;
				}
			}
			return null;
		}

		/**
		 * @param Media $media
		 * @return $this
		 */
		public function removeMedia(Media $media){
			$i = $this->searchMedia($media);
			if($i !== false){
				array_splice($this->medias,$i,1);
			}
			return $this;
		}


		/**
		 * @param Animation $animation
		 * @return $this
		 */
		public function addAnimation(Animation $animation){
			if(!$animation->getName()){
				throw new \LogicException('Document.addAnimation($Animation) , Animation is not named');
			}
			if(!$this->getAnimation($animation)){
				$this->animations[] = $animation;
			}
			return $this;
		}

		/**
		 * @param Animation $animation
		 * @return mixed
		 */
		public function searchAnimation(Animation $animation){
			return array_search($animation,$this->animations,true);
		}

		/**
		 * @param $animation
		 * @return Animation|null
		 */
		public function getAnimation($animation){
			if($animation instanceof Animation){
				$animation = $animation->getName();
			}
			foreach($this->animations as $a){
				if($a->getName() === $animation){
					return $a;
				}
			}
			return null;
		}

		/**
		 * @param Animation $animation
		 * @return $this
		 */
		public function removeAnimation(Animation $animation){
			$i = $this->searchAnimation($animation);
			if($i !== false){
				array_splice($this->animations, $i, 1);
			}
			return $this;
		}


		/**
		 * @param bool $pretty
		 */
		public static function setPrettyMode($pretty = true){
			self::$pretty_mode = $pretty;
		}

		/**
		 * @return bool
		 */
		public static function isPrettyMode(){
			return self::$pretty_mode;
		}

		/**
		 * @param string $char
		 */
		public static function setPrettyIndent($char = "\t"){
			self::$pretty_indent = $char;
		}

		/**
		 * @return string
		 */
		public static function getPrettyIndent(){
			return self::$pretty_indent;
		}


		/**
		 * @return void
		 */
		public function update(){
			// TODO: Implement update() method.
		}
	}

}