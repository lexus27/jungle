<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.09.2016
 * Time: 15:35
 */
namespace Jungle\Data\Record\Exception\Field {

	use Jungle\Data\Record\Head\Field;
	use Jungle\Util\Data\Foundation\Validation\Message\AggregationMessageException;
	use Jungle\Util\Data\Foundation\Validation\Message\MessageInterface;

	/**
	 * Class FieldAggregationMessageException
	 * @package Jungle\Data\Record\Head\Field
	 */
	class FieldAggregationMessageException extends AggregationMessageException{

		/** @var  Field */
		protected $field;

		/** @var string  */
		protected $type = 'FieldAggregation';

		/**
		 * FieldAggregationMessageException constructor.
		 * @param Field $field
		 * @param MessageInterface[] $messages
		 * @param array $info
		 * @param string $systemMessage
		 */
		public function __construct(Field $field, array $messages,array $info = [], $systemMessage = ''){
			$this->field = $field;
			parent::__construct($messages, $info, $systemMessage);
		}

		/**
		 * @return Field|string
		 */
		public function getField(){
			return $this->field;
		}

	}
}

