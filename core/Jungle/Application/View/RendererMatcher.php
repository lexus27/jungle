<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 11.07.2016
 * Time: 20:16
 */
namespace Jungle\Application\View {
	
	use Jungle\Application\Dispatcher\Controller\ProcessInterface;
	use Jungle\Application\ViewInterface;

	/**
	 * Class RendererMatcher
	 * @package Jungle\Application\View
	 */
	class RendererMatcher implements RendererMatcherInterface{

		/** @var  callable[]  */
		protected $rules = [];

		protected $default_renderer_alias;

		/** @var  bool  */
		protected $rulesSorted;

		/**
		 * RendererMatcher constructor.
		 * @param $default_renderer_alias
		 */
		public function __construct($default_renderer_alias){
			$this->default_renderer_alias = $default_renderer_alias;
		}

		/**
		 * @param $renderer_alias
		 * @param callable $rule
		 * @param int $priority
		 * @return $this
		 */
		public function addRule($renderer_alias, callable $rule, $priority = 0){
			$this->rules[$renderer_alias] = [$rule,$priority];
			$this->rulesSorted = false;
			return $this;
		}

		/**
		 * @return $this
		 */
		public function sortRules(){
			static $cmp = null;
			if(!$cmp){
				$cmp = function($a,$b){
					if($a[1] === $b[1]){
						return 0;
					}
					return $a[1] > $b[1]?1:-1;
				};
			}
			usort($this->rules,$cmp);
			$this->rulesSorted = true;
			return $this;
		}

		/**
		 * @param $renderer_alias
		 * @param $priority
		 * @return $this
		 */
		public function setPriority($renderer_alias, $priority = 0){
			if(isset($this->rules[$renderer_alias])){
				if($priority !== $this->rules[$renderer_alias][1]){
					$this->rules[$renderer_alias][1] = $priority;
					$this->rulesSorted = false;
				}
			}
			return $this;
		}

		/**
		 * @param ProcessInterface $process
		 * @param ViewInterface $view
		 * @return string
		 */
		public function __invoke(ProcessInterface $process, ViewInterface $view){
			if(!$this->rulesSorted){
				$this->sortRules();
			}
			foreach($this->rules as $rendererName => list($rule, $priority)){
				if(call_user_func($rule, $process, $view)){
					return $rendererName;
				}
			}
			return $this->default_renderer_alias;
		}
	}
}

