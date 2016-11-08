<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.10.2016
 * Time: 16:15
 */
namespace Jungle\Util\Communication\Hypertext\Header\Concrete {

	use Jungle\Util\Communication\Hypertext\Header;
	use Jungle\Util\Communication\Hypertext\Header\Value;
	use Jungle\Util\Communication\Hypertext\HeaderRegistryInterface;

	/**
	 * Class ContentEncoding
	 * @package Jungle\Util\Communication\Hypertext\Header\Concrete
	 */
	class ContentEncoding extends Header{

		/** @var int  */
		protected $priority_encode = 1000;

		/** @var int  */
		protected $priority_decode = 1003;


		/**
		 * @param Value[] $values
		 * @param $contents
		 * @param HeaderRegistryInterface $headers
		 * @return null|string
		 * @throws \Exception
		 */
		public function decodeContents(array $values, $contents, HeaderRegistryInterface $headers){
			$value = array_pop($values);
			if($value){
				if(stripos($value,'gzip')){
					return gzdecode($contents);
				}elseif(stripos($value,'deflate')){
					return gzinflate($contents);
				}elseif(stripos($value,'lzma')){

				}elseif(stripos($value,'sdch')){

				}elseif(stripos($value,'br')){

				}
				throw new \Exception('Compressor "'.$value.'" not found');
			}
			return null;
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
			if($value){
				if(stripos($value,'gzip')){
					return gzencode($contents);
				}elseif(stripos($value,'deflate')){
					return gzdeflate($contents);
				}elseif(stripos($value,'lzma')){

				}elseif(stripos($value,'sdch')){

				}elseif(stripos($value,'br')){

				}
				throw new \Exception('Compressor "'.$value.'" not found');
			}
			return null;
		}

	}
}

