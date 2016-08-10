<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.12.2015
 * Time: 23:42
 */
namespace Jungle {

	use Jungle\Messenger\Combination;
	use Jungle\Messenger\ICombination;
	use Jungle\Messenger\IContact;

	/**
	 * Class Messenger
	 * @package Jungle
	 */
	abstract class Messenger{

		/** @var array */
		protected $options = [];

		/**
		 * @param array $options
		 */
		public function __construct(array $options = []){
			$this->options = $options;
		}

		/**
		 * @param Combination $combination
		 */
		public function send(Combination $combination){
			$this->begin($combination);
			foreach($combination->getDestinations() as $destination){
				$this->registerDestination($destination);
			}
			$this->complete($combination);
		}

		/**
		 * @param ICombination $combination
		 * @return void
		 */
		abstract protected function begin(ICombination $combination);

		/**
		 * @param IContact $destination
		 * @return void
		 */
		abstract protected function registerDestination(IContact $destination);

		/**
		 * @param ICombination $combination
		 * @return void
		 */
		abstract protected function complete(ICombination $combination);

	}
}

