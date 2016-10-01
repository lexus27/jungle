<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.02.2016
 * Time: 3:35
 */
namespace Jungle\TypeHint\Rule\Builder {

	use Jungle\TypeHint;
	use Jungle\TypeHint\Rule;
	use Jungle\TypeHint\Rule\Builder;
	use Jungle\TypeHint\Rule\Complex;
	use Jungle\Util\Value\Massive;

	/**
	 * Class MultiBuilder
	 * @package Jungle\TypeHint\Rule\Builder
	 */
	class MultiBuilder extends Builder{

		protected $name = 'general_builder';

		/**
		 * @var Builder[]
		 */
		protected $builders = [];

		/**
		 * @param Builder $builder
		 * @return $this
		 */
		public function addBuilder(Builder $builder){
			if($this->searchBuilder($builder)===false){
				$this->builders[] = $builder;
			}
			return $this;
		}

		/**
		 * @param Builder $builder
		 * @return mixed
		 */
		public function searchBuilder(Builder $builder){
			return array_search($builder,$this->builders,true);
		}

		/**
		 * @param Builder $builder
		 * @return $this
		 */
		public function removeBuilder(Builder $builder){
			if(($i = $this->searchBuilder($builder))!==false){
				array_splice($this->builders,$i,1);
			}
			return $this;
		}

		/**
		 * @param $name
		 * @return \Jungle\Util\NamedInterface|null
		 */
		public function getBuilder($name){
			return Massive::getNamed($this->builders,$name,'strcasecmp');
		}


		/**
		 * @param $definition
		 * @return bool
		 */
		public function checkSuit($definition){
			return true;
		}

		/**
		 * @param $definition
		 * @param TypeHint $hinter
		 * @return Rule
		 */
		public function build($definition, TypeHint $hinter){
			foreach($this->builders as $builder){
				if($builder->checkSuit($definition)){
					return $builder->build($definition,$hinter);
				}
			}
			return null;
		}

		/**
		 * @param $definition
		 * @param TypeHint $hinter
		 * @return Complex
		 */
		public function buildComplex($definition, TypeHint $hinter){
			foreach($this->builders as $builder){
				if($builder->checkSuit($definition)){
					return $builder->buildComplex($definition,$hinter);
				}
			}
			return null;
		}

	}
}

