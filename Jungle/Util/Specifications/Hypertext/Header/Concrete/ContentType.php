<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 11.01.2016
 * Time: 1:25
 */
namespace Jungle\Util\Specifications\Hypertext\Header\Concrete {

	use Jungle\Util\Specifications\Hypertext\Content\Multipart;
	use Jungle\Util\Specifications\Hypertext\Header;
	use Jungle\Util\Specifications\Hypertext\Header\Value;
	use Jungle\Util\Specifications\Hypertext\HeaderRegistryInterface;

	/**
	 * Class ContentType
	 * @package Jungle\Util\Specifications\Hypertext\Header\Concrete
	 */
	class ContentType extends Header{

		/** @var int  */
		protected $priority_encode = 1001;

		/** @var int  */
		protected $priority_decode = 1002;


		/**
		 * @param Value[] $values
		 * @param $contents
		 * @param HeaderRegistryInterface $headers
		 * @return null|string
		 * @throws \Exception
		 */
		public function decodeContents(array $values, $contents, HeaderRegistryInterface $headers){
			$value = array_pop($values);
			$value = Header::parseHeaderValue($value);
			$modified = false;
			if(isset($value['params']['charset'])){
				if(strcasecmp($value['params']['charset'], 'utf-8')!==0){
					$modified = true;
					$contents = mb_convert_encoding($contents, 'utf-8', $value['params']['charset']);
				}
			}

			if(stripos($value['value'],'multipart')!==false){
				$modified = true;
				$contents = new Multipart($contents,$headers);
			}
			return $modified?$contents:null;
		}



		/**
		 * @param Value[] $values
		 * @param $contents
		 * @param HeaderRegistryInterface $headers
		 * @return null|string
		 * @throws \Exception
		 */
		public function encodeContents(array $values, $contents, HeaderRegistryInterface $headers){
			$value = array_pop($values);
			$value = Header::parseHeaderValue($value);
			$modified = false;
			if(isset($value['params']['charset'])){
				if(strcasecmp($value['params']['charset'], 'utf-8')!==0){
					$modified = true;
					$contents = mb_convert_encoding($contents, $value['params']['charset'], 'utf-8');
				}
			}
			return $modified?$contents:null;
		}


	}
}

