<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.01.2016
 * Time: 1:27
 */
namespace Jungle\Specifications\TextTransfer\Header\Concrete {

	use Jungle\Specifications\TextTransfer\Header;
	use Jungle\Specifications\TextTransfer\Header\Value;

	/**
	 * Class ContentTransferEncoding
	 * @package Jungle\HeaderCover\Header\Concrete
	 */
	class ContentTransferEncoding extends Header{

		/** @var int */
		protected $prepareTurn = 1002;

		/** @var int  */
		protected $recoverTurn = 1001;

		/**
		 * @param Value[] $values
		 * @param $body
		 */
		public function onDocumentPrepareBody(array $values, &$body){
			$value = array_pop($values);
			$value = $value->getValue();
			if(strcasecmp($value,'base64') === 0){
				$body = chunk_split(base64_encode($body),71);
			}
			if(strcasecmp($value,'quoted-printable') === 0){
				$body = chunk_split(quoted_printable_encode($body),71);
			}
		}

		/**
		 * @param Value[] $values
		 * @param $body
		 */
		public function onDocumentRecoverBody(array $values,&$body){
			$value = array_pop($values);
			$value = $value->getValue();
			if(strcasecmp($value,'base64') === 0){
				$body = base64_decode(preg_replace('@[\r\n\t]+@','',trim($body)));
			}
			if(strcasecmp($value,'quoted-printable') === 0){
				$body = quoted_printable_decode(preg_replace('@[\r\n\t]+@','',trim($body)));
			}
		}

	}
}

