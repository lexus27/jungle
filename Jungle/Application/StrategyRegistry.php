<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.10.2016
 * Time: 10:45
 */
namespace Jungle\Application {

	/**
	 * Class StrategyRegistry
	 * @package Jungle\Application
	 *
	 * Вынес логику отвечающую за взаимодействие со стратегиями сюда из диспетчера.
	 *
	 */
	class StrategyRegistry{

		/** @var  StrategyInterface[] */
		protected $strategies = [];

		/** @var  array[]  */
		protected $strategies_definitions = [];

		/** @var array|null  */
		protected $strategies_order;


		/**
		 * @param StrategyInterface|string $alias
		 * @param StrategyInterface|string $strategy
		 * @param float $priority
		 * @return $this
		 */
		public function setStrategy($alias, $strategy, $priority = 0.0){
			if(is_string($strategy)){
				$this->strategies[$alias] = null;
				$this->strategies_definitions[$alias] = [
					'class'     => $strategy,
					'priority'  => floatval($priority)
				];
				$this->strategies_order = null;
			}elseif($strategy instanceof StrategyInterface){
				$this->strategies[$alias] = null;
				$this->strategies_definitions[$alias] = [
					'class'    => get_class($strategy),
					'priority' => floatval($priority)
				];
				$this->strategies_order = null;
			}
			return $this;
		}

		/**
		 * @param $alias
		 * @param $priority
		 * @return $this
		 */
		public function setStrategyPriority($alias, $priority){
			if(!isset($this->strategies_definitions[$alias])){
				throw new \LogicException('Strategy "'.$alias.'" not defined!');
			}
			$this->strategies_definitions[$alias]['priority'] = floatval($priority);
			return $this;
		}


		/**
		 * @param array $strategies
		 */
		public function setStrategies(array $strategies){
			$this->strategies = [];
			$this->strategies_definitions = [];
			$this->strategies_order = [];
			$i = 0;
			foreach($strategies as $alias => $strategy){
				$this->strategies_order[] = $alias;
				if(is_array($strategy)){
					$this->strategies[$alias] = null;
					$this->strategies_definitions[$alias] = array_replace($strategy,['priority' => $i]);
				}elseif(is_string($strategy)){
					$this->strategies[$alias] = null;
					$this->strategies_definitions[$alias] = [
						'class'     => $strategy,
						'priority'  => floatval($i)
					];
				}elseif($strategy instanceof StrategyInterface){
					$this->strategies[$alias] = null;
					$this->strategies_definitions[$alias] = [
						'class'    => get_class($strategy),
						'priority' => floatval($i)
					];
				}
				$i = $i + 5;
			}
		}

		/**
		 * @param $alias
		 * @return StrategyInterface
		 * @throws Exception
		 */
		public function getStrategy($alias){
			if(!isset($this->strategies_definitions[$alias])){
				throw new \LogicException('Strategy "'.$alias.'" not defined!');
			}
			if(!isset($this->strategies[$alias])){
				$strategy = $this->_loadStrategy($alias, $this->strategies_definitions[$alias]);
				$this->strategies[$alias] = $strategy;
			}
			return $this->strategies[$alias];
		}

		/**
		 * @param RequestInterface $request
		 * @return StrategyInterface|null
		 */
		public function matchStrategy(RequestInterface $request){
			if($this->strategies_order === null){
				$this->strategies_order = array_keys($this->strategies_definitions);
				usort($this->strategies_order, [$this,'_strategy_sort']);
			}
			foreach($this->strategies_order as $name){
				$definition = $this->strategies_definitions[$name];
				/** @var StrategyInterface $className */
				$className = $definition['class'];
				$strategy = $this->strategies[$name];
				if($className::check($request)){
					if(!$strategy){
						$this->strategies[$name] = $strategy = $this->_loadStrategy($name, $definition);
					}
					return $strategy;
				}
			}
			return null;
		}

		protected function _strategy_sort($a,$b){
			$a = $this->strategies_definitions[$a]['priority'];
			$b = $this->strategies_definitions[$b]['priority'];
			if($a == $b){
				return 0;
			}
			return $a > $b?1:-1;
		}


		/**
		 * @param $alias
		 * @param array $definition
		 * @return StrategyInterface
		 * @throws Exception
		 */
		protected function _loadStrategy($alias,array $definition){
			if(!isset($definition['class'])){
				throw new Exception('Strategy load error: "' . $alias . '" not defined class in definition');
			}
			$className = $definition['class'];
			if(class_exists($className)){
				$strategy = new $className();
				if(!$strategy instanceof StrategyInterface){
					throw new Exception('Module instance is not '.StrategyInterface::class);
				}
				$strategy->setName($alias);
				return $strategy;
			}else{
				throw new Exception('Strategy load error: "' . $alias . '" not found strategy class "' . $className . '"');
			}
		}

	}
}

