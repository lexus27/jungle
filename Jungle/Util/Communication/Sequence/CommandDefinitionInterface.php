<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 22:58
 */
namespace Jungle\Util\Communication\Sequence {

	/**
	 * Interface CommandDefinitionInterface
	 * @package Jungle\Util\Communication\Sequence
	 */
	interface CommandDefinitionInterface{


		/**
		 * @param $definition
		 * @return mixed
		 */
		public function setDefinition($definition);

		/**
		 * @return mixed
		 */
		public function getDefinition();

		/**
		 * @param RuleInterface $rule
		 * @return mixed
		 */
		public function addRule(RuleInterface $rule);

		/**
		 * @return mixed
		 */
		public function getRules();



	}
}

