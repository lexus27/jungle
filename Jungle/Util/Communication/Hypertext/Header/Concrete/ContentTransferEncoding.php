<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.01.2016
 * Time: 1:27
 */
namespace Jungle\Util\Communication\Hypertext\Header\Concrete {

	use Jungle\Util\Communication\Hypertext\Header;
	use Jungle\Util\Communication\Hypertext\Header\Value;
	use Jungle\Util\Communication\Hypertext\HeaderRegistryInterface;

	/**
	 * Class ContentTransferEncoding
	 * @package Jungle\HeaderCover\Header\Concrete
	 */
	class ContentTransferEncoding extends Header{

		/** @var int */
		protected $priority_encode = 1002;

		/** @var int  */
		protected $priority_decode = 1001;

		/**
		 * @param Value[] $values
		 * @param $contents
		 * @param HeaderRegistryInterface $headers
		 * @return null|string
		 */
		public function encodeContents(array $values, $contents, HeaderRegistryInterface $headers){
			$value = array_pop($values);
			if(stripos($value,'base64') !== false){
				return chunk_split(base64_encode($contents),71);
			}
			if(stripos($value,'quoted-printable') !== false){
				return quoted_printable_encode($contents);
			}
			return null;
		}

		/**
		 * @param array $values
		 * @param $contents
		 * @param HeaderRegistryInterface $headers
		 * @return null|string
		 */
		public function decodeContents(array $values, $contents, HeaderRegistryInterface $headers){
			$value = array_pop($values);
			if(stripos($value,'base64') !== false){
				return base64_decode(preg_replace("@[\r\n\t]+@",'',$contents));
			}
			if(stripos($value,'quoted-printable') !== false){
				return quoted_printable_decode($contents);
			}
			return null;
		}

	}
}

