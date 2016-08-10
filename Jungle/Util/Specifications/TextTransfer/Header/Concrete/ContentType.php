<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 11.01.2016
 * Time: 1:25
 */
namespace Jungle\Util\Specifications\TextTransfer\Header\Concrete {

	use Jungle\Util\Specifications\TextTransfer\Body\Multipart;
	use Jungle\Util\Specifications\TextTransfer\Header;
	use Jungle\Util\Specifications\TextTransfer\Header\Value;

	class ContentType extends Header{

		/**
		 * @var int
		 */
		protected $prepareTurn = 1001;

		protected $recoverTurn = 1002;

		/**
		 * @param Value[] $values
		 * @param $body
		 */
		public function onDocumentRecoverBody(array $values,&$body){
			$value = array_pop($values);
			if(stripos($value->getValue(),'multipart')!==false){
				$bound = $value->getParam('boundary');
				$body = Multipart::parse($body,$bound);
			}
		}



	}
}

