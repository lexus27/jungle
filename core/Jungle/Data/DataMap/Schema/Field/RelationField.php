<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.03.2016
 * Time: 12:47
 */
namespace Jungle\Data\DataMap\Schema\Field {

	use Jungle\Data\DataMap\Schema\Field;

	/**
	 * Class RelationField
	 * @package Jungle\Data\DataMap\Schema\Field
	 */
	class RelationField extends Field{

		/** @var   */
		protected $belongs;

		/** @var   */
		protected $many;

		/** @var   */
		protected $one;

		/**
		 * @var string|int
		 * Название поля схемы,
		 */
		protected $from;

		protected $relation_collection;

		protected $association;


		/**
		 * @param $item
		 */
		protected function _loadRelated($item){

		}

	}
}

