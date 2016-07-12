<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.07.2016
 * Time: 3:04
 */
namespace Jungle\Application {
	
	use Jungle\Application\Dispatcher\Controller\ProcessInterface;
	use Jungle\Application\View\RendererInterface;

	/**
	 * Class View
	 * @package Jungle\Application\View
	 */
	class View implements ViewInterface{

		/** @var RendererInterface[] */
		protected $renderer_collection = [];

		/** @var  callable */
		protected $matcher;

		/**
		 * View constructor.
		 * @param callable $matcher
		 */
		public function __construct(callable $matcher = null){
			$this->matcher = $matcher;
		}

		/**
		 * @param callable $matcher
		 * @return $this
		 */
		public function setMatcher(callable $matcher){
			$this->matcher = $matcher;
			return $this;
		}

		/**
		 * @return callable
		 */
		public function getMatcher(){
			return $this->matcher;
		}

		/**
		 * @param ProcessInterface $process
		 * @return string
		 * */
		public function match(ProcessInterface $process){
			return call_user_func($this->matcher, $process,$this);
		}

		/**
		 * @param $name
		 * @param RendererInterface $renderer
		 * @return $this
		 */
		public function addRenderer($name, View\RendererInterface $renderer){
			$this->renderer_collection[$name] = $renderer;
			return $this;
		}

		/**
		 * @param $name
		 * @return RendererInterface|null
		 */
		public function getRenderer($name){
			return isset($this->renderer_collection[$name])? $this->renderer_collection[$name]:null;
		}

		/**
		 * @param ProcessInterface $process
		 * @param null $renderer_name
		 * @return string
		 */
		public function render(ProcessInterface $process, $renderer_name = null){
			if(is_null($renderer_name)){
				$renderer_name = $this->match($process);
				if(!$renderer_name){
					throw new \LogicException('Invalid display name matching');
				}
			}elseif(!$renderer_name){
				throw new \LogicException('Invalid supplied display name');
			}
			$renderer = $this->getRenderer($renderer_name);
			if(!$renderer){
				throw new \LogicException('Not found "' . $renderer_name . '" display!');
			}
			$renderer->initialize();
			return $renderer->render($process);
		}

	}
}

