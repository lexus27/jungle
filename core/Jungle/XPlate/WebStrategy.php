<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 11.09.2015
 * Time: 19:11
 */
namespace Jungle\XPlate {

	use Jungle\XPlate\Interfaces\IWebStrategy;
	use Jungle\XPlate\Interfaces\IWebSubject;
	use Jungle\XPlate\Services\AttributePool;
	use Jungle\XPlate\Services\StylePool;
	use Jungle\XPlate\Services\TagPool;

	/**
	 * Class Strategy
	 * @package Jungle\XPlate\Services
	 *
	 * Сборка сервисов, настроек и обязательных объектных пулов , которой пользуется область HTML/CSS/JS
	 *
	 */
	class WebStrategy implements IWebStrategy{

		use \Jungle\Util\ServiceContainerTrait;
		use \Jungle\Util\OptionContainerTrait;

		protected $locked = false;

		/**
		 * @var IWebSubject[]
		 */
		protected $subjects = [];

		/**
		 * @var IWebStrategy
		 */
		protected static $default;

		/**
		 * @param AttributePool $m
		 * @return $this
		 */
		public function setAttributeManager($m){
			if(!$this->isLocked())$this->setService('attributeManager',$m);
			return $this;
		}

		/**
		 * @return AttributePool
		 */
		public function getAttributeManager(){
			return $this->getService('attributeManager');
		}


		/**
		 * @param TagPool $m
		 * @return $this
		 */
		public function setTagManager($m){
			if(!$this->isLocked())$this->setService('tagManager',$m);
			return $this;
		}

		/**
		 * @return TagPool
		 */
		public function getTagManager(){
			return $this->getService('tagManager');
		}


		/**
		 * @param StylePool $m
		 * @return $this
		 */
		public function setStyleManager($m){
			if(!$this->isLocked()) $this->setService('styleManager', $m);
			return $this;
		}

		/**
		 * @return StylePool
		 */
		public function getStyleManager(){
			return $this->getService('styleManager');
		}

		/**
		 * @return bool
		 */
		public function isValid(){
			return
				$this->getTagManager() instanceof TagPool &&
				$this->getStyleManager() instanceof StylePool &&
				$this->getAttributeManager() instanceof AttributePool;
		}

		/**
		 * @return $this
		 */
		public function lock(){
			if($this->isValid()){
				$this->locked = true;
			}
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isLocked(){
			return $this->locked;
		}

		/**
		 * @return IWebStrategy
		 */
		public static function getDefault(){
			if(!self::$default){
				self::$default = new WebStrategy();
			}
			return self::$default;
		}

		/**
		 * @param IWebStrategy $strategy
		 * @return mixed
		 */
		public static function setDefault(IWebStrategy $strategy){
			self::$default = $strategy;
		}


		/**
		 * Обновить все документы в связи с изменением сервисов стратегии
		 */
		protected function update(){
			foreach($this->subjects as $subject){
				$subject->refreshStrategy();
			}
		}

		/**
		 * @param IWebSubject $subject
		 * @param bool|false $appliedInSubject
		 * @return $this
		 */
		public function addSubject(IWebSubject $subject, $appliedInSubject = false){
			if($this->searchSubject($subject)===false){
				$this->subjects[] = $subject;
				if(!$appliedInSubject){
					$subject->setStrategy($this,true);
				}
			}
			return $this;
		}

		/**
		 * @param IWebSubject $subject
		 * @return mixed
		 */
		public function searchSubject(IWebSubject $subject){
			return array_search($subject,$this->subjects,true);
		}

		/**
		 * @param IWebSubject $subject
		 * @param bool|false $appliedInSubject
		 * @return $this
		 */
		public function removeSubject(IWebSubject $subject, $appliedInSubject = false){
			if(($i = $this->searchSubject($subject))!==false){
				array_splice($this->subjects,$i,1);
				if(!$appliedInSubject){
					$subject->setStrategy(null,true,true);
				}
			}
			return $this;
		}

		/**
		 */
		protected function onOptionChanged(){
			$this->update();
		}
	}
}

