<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 29.09.2016
 * Time: 10:05
 */
namespace Jungle\User\AccessControl\Matchable\Resolver {

	use Jungle\Data\Record\Head\Field;
	use Jungle\User\AccessControl\Context;
	use Jungle\User\AccessControl\Context\ObjectAccessor;
	use Jungle\User\AccessControl\ContextInterface;
	use Jungle\User\AccessControl\Matchable;
	use Jungle\User\AccessControl\Matchable\Result;
	use Jungle\Util\Value\Massive;

	/**
	 * Class PredicateInspector
	 * @package Jungle\User\AccessControl\Matchable\Resolver
	 */
	class PredicateInspector extends Inspector{

		/** @var array  */
		protected $predicate = [];

		/** @var  mixed */
		protected $target_effect;

		/**
		 * @param ContextInterface $context
		 * @param Result $result
		 * @param $expression
		 * @return $this
		 */
		public function beginInspect(ContextInterface $context, Result $result, $expression){
			$this->context = $context;
			$this->result = $result;
			$this->expression = $expression;
			$this->predicate = [];
			return $this;
		}


		/**
		 * @param $target_effect
		 * @return $this
		 */
		public function setTargetEffect($target_effect){
			$this->target_effect = Matchable::friendlyEffect($target_effect);
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getTargetEffect(){
			return $this->target_effect;
		}

		/**
		 * @param $chunk
		 * @param $depth
		 * @param $container
		 * @param $path
		 * @param $fullPath
		 * @param $definition
		 */
		public function onNotFound($chunk, $depth, $container, $path, $fullPath, $definition){
			if($depth === 1 && !$path && $container instanceof ObjectAccessor){
				$this->predicate[$this->process_key] = new Field($chunk);
			}
		}

		/**
		 * @param $result
		 * @return mixed
		 */
		public function checkoutResult($result){
			if(!$result){
				if($this->predicate && $this->target_effect === $this->result->getMatchableEffect()){
					$predicate = array_replace($this->processed, $this->predicate);
					$key = 'predicate'.($this->mode?'_'.$this->mode:'');
					$this->result->addData($key, [$predicate['left'], $predicate['operator'], $predicate['right']]);
					return true;
				}
			}
			return $result;
		}


		/**
		 * @param ContextInterface $context
		 * @param Result $result
		 * @return array|null
		 */
		public function collectCondition(ContextInterface $context, Result $result){
			$anyOf = $result->getData('predicate_any_of');
			$allOf = $result->getData('predicate_all_of');
			$clause = [];
			if($anyOf){
				$chunk = [];
				foreach($anyOf as $item){
					list($l,$o,$r) = $item;
					if($l instanceof Field){
						$l = $l->getName();
					}
					if($r instanceof Field){
						$r = $r->getName();
					}
					$chunk[] = [$l,$o,$r];
				}
				if($chunk){
					$clause[] = Massive::insertSeparates($chunk,'OR');
				}
			}

			if($allOf){
				$chunk = [];
				foreach($allOf as $item){
					list($l,$o,$r) = $item;
					if($l instanceof Field){
						$l = $l->getName();
					}
					if($r instanceof Field){
						$r = $r->getName();
					}
					$chunk[] = [$l,$o,$r];
				}
				if($chunk){
					$clause[] = Massive::insertSeparates($chunk,'AND');
				}
			}

			$children = $result->getChildren();
			if($children){
				$chunk = [];
				foreach($children as $childResult){
					$collected = $this->collectCondition($context, $childResult);
					if($collected){
						$chunk[] = $collected;
					}
				}
				if($chunk){
					$clause[] = Massive::insertSeparates($chunk,'OR');
				}
			}
			return $clause?$this->normalizeConditions(Massive::insertSeparates($clause, 'AND')):null;
		}

		/**
		 * @param array $condition
		 * @return array|mixed
		 */
		public function normalizeConditions(array $condition){
			$count = count($condition);
			$current = current($condition);
			if($count === 3 && !is_array($current)){
				return $condition;
			}elseif($count===1){
				if(is_array($current)){
					return $this->normalizeConditions($current);
				}else{
					return $current;
				}
			}else{
				foreach($condition as & $item){
					if(is_array($item)){
						$item = $this->normalizeConditions($item);
					}
				}
				return $condition;
			}
		}

	}
}

