<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 10.05.2015
 * Time: 21:32
 */

namespace Jungle\XPlate\CSS\Selector {

	use Jungle\Util\INamed;
	use Jungle\Util\Smart\Keyword\Keyword;
	use Jungle\XPlate\HTML\IElement;

	/**
	 * Class Dependency
	 * @package Jungle\XPlate\CSS\Selector
	 *
	 * Зависимость в селекторе:
	 * $dependency1 = '>';
	 * $dependency2 = '+';
	 * $dependency3 = '~';
	 * $dependency4 = ' ';
	 * #identifier <$dependency1 = '>' >    .class1.class2 <$dependency2 = ' '> .class3:hover
	 * #identifier       >                  .class1.class2                      .class3:hover
	 * #identifier > .class1.class2 .class3:hover
	 */
	class Dependency extends Keyword implements INamed{

		/**
		 * @var string
		 */
		protected $symbol = ' ';

		/**
		 * @var callable
		 */
		protected $handler;

		/**
		 * @return string
		 */
		public function getName(){
			return $this->getIdentifier();
		}

		/**
		 * @param string $symbol
		 */
		public function setName($symbol = ' '){
			$this->setIdentifier($symbol);
		}

		/**
		 * @param callable $handler
		 */
		public function setHandler(callable $handler){
			$this->handler = $handler;
		}

		/**
		 * @param Combination $combination
		 * @param IElement $element
		 * @return IElement[]
		 */
		public function search(Combination $combination, IElement $element){
			if(!is_callable($this->handler)){
				throw new \LogicException('XPlate.CSS.Dependency['.$this->symbol .'].handler is not set');
			}
			return call_user_func($this->handler,$combination,$element);
		}

		/**
		 * @param Combination $combination
		 * @param IElement $element
		 * @return \Jungle\XPlate\HTML\IElement[]
		 */
		public function __invoke(Combination $combination, IElement $element){
			return $this->search($combination,$element);
		}

	}
}