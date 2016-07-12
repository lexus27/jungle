<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.07.2016
 * Time: 21:20
 */
namespace Jungle\Application\View\Renderer\Data {

	use Jungle\Application\View\Renderer\Data;

	/**
	 * Class Xml
	 * @package Jungle\Application\View\Renderer\Data
	 */
	class Xml extends Data{

		protected $type = 'xml';

		protected $version = '1.0';

		public function setVersion($version){
			$this->version = $version;
			return $this;
		}

		/** @var  \XMLWriter */
		protected $writer;

		/**
		 * @param $data
		 * @return string
		 */
		public function convert($data){
			if(!$this->writer){
				$this->writer = new \XMLWriter();
			}
			$xml = $this->writer;
			$xml->startDocument($this->version,'utf-8');
			$this->_parseValue($xml, $data);
			$xml->endDocument();
			return $xml->flush();
		}

		/**
		 * @param \XMLWriter $xml
		 * @param $value
		 */
		protected function _parseValue(\XMLWriter $xml, $value){
			if(is_array($value) || $value instanceof \Traversable){
				foreach($value as $k => $v){
					$xml->startElement($k);
					$this->_parseValue($xml, $value);
					$xml->endElement();
				}
			}else{
				$xml->writeRaw($value);
			}
		}

	}
}

