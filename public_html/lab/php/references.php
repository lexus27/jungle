<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 31.03.2016
 * Time: 19:28
 */
namespace php_references;


abstract class ExampleAbstract{

	protected $_array = [];

	/**
	 * @param array $array
	 */
	public function __construct(array $array){
		$this->_array = & $array;
	}


	abstract public function getArrayItemReferences();

}
class SimpleArrayAsReference extends ExampleAbstract{

	/**
	 * @return array
	 */
	public function & getArrayItemReferences(){
		return $this->_array;
	}

}
class ItemsConvertToNewArray extends ExampleAbstract{

	public function getArrayItemReferences(){
		$a = [];
		foreach($this->_array as $i => & $itm){
			$a[$i] = & $itm;
		}
		return $a;
	}
}
class ItemsConvertToNewArrayWithFunction extends ExampleAbstract{


	public function getArrayItemReferences(){
		return $this->_toReferences($this->_array);
	}

	protected function _toReferences(array & $array){
		$a = [];
		foreach($array as $i => & $itm){
			$a[$i] = & $itm;
		}
		return $a;
	}

}


class ItemsConvertToNewArrayWithFunctionNotReferencePass extends ExampleAbstract{

	public function getArrayItemReferences(){
		return $this->_toReferences($this->_toReferences1($this->_array));
	}

	protected function _toReferences1(array & $array){
		$a = [];
		foreach($array as $i => & $itm){
			$a[$i] = & $itm;
		}
		return $a;
	}

	protected function _toReferences(array $array){
		$a = [];
		foreach($array as $i => & $itm){
			$a[$i] = & $itm;
		}
		return $a;
	}

}

$examples_array = [
	'ford.fusion',
	function(){echo 'A';},
	function(){echo 'A';},
	new \stdClass()
];
echo '<h1>Unset(Array[Item]) test</h1>';
$example1 = new SimpleArrayAsReference($examples_array);
$itms = & $example1->getArrayItemReferences();
unset($itms[2]);
$example2 = new ItemsConvertToNewArray($examples_array);
unset($example2->getArrayItemReferences()[2]);
$example3 = new ItemsConvertToNewArrayWithFunction($examples_array);
unset($example3->getArrayItemReferences()[2]);
$example4 = new ItemsConvertToNewArrayWithFunctionNotReferencePass($examples_array);
unset($example4->getArrayItemReferences()[2]);
echo '<pre>';
var_dump($example1,$example2,$example3,$example4);
echo '</pre>';

echo '<h1>Unset(item) test</h1>';
$example1 = new SimpleArrayAsReference($examples_array);
$itm1 = & $example1->getArrayItemReferences()[2];
unset($itm1);
$example2 = new ItemsConvertToNewArray($examples_array);
$itm2 = & $example2->getArrayItemReferences()[2];
unset($itm2);
$example3 = new ItemsConvertToNewArrayWithFunction($examples_array);
$itm3 = & $example3->getArrayItemReferences()[2];
unset($itm3);
$example4 = new ItemsConvertToNewArrayWithFunctionNotReferencePass($examples_array);
$itm4 = & $example4->getArrayItemReferences()[2];
unset($itm4);
echo '<pre>';
var_dump($example1,$example2,$example3,$example4);
echo '</pre>';


echo '<h1>$Array[Item] set with reference</h1>';
$example1 = new SimpleArrayAsReference($examples_array);
$itms = & $example1->getArrayItemReferences();
$itms[2] = null;
$example2 = new ItemsConvertToNewArray($examples_array);
$example1->getArrayItemReferences()[2] = null;
$example3 = new ItemsConvertToNewArrayWithFunction($examples_array);
$example1->getArrayItemReferences()[2] = null;
$example4 = new ItemsConvertToNewArrayWithFunctionNotReferencePass($examples_array);
$example1->getArrayItemReferences()[2] = null;
echo '<pre>';
var_dump($example1,$example2,$example3,$example4);
echo '</pre>';


echo '<h1>$Item set with reference</h1>';
$example1 = new SimpleArrayAsReference($examples_array);
$itms = & $example1->getArrayItemReferences();
$itm1 = & $itms[2];
$itm1 = null;
unset($itm1);
$example2 = new ItemsConvertToNewArray($examples_array);
$itm2 = & $example2->getArrayItemReferences()[2];
$itm2 = null;unset($itm2);
$example3 = new ItemsConvertToNewArrayWithFunction($examples_array);
$itm3 = & $example3->getArrayItemReferences()[2];
$itm3 = null;unset($itm3);
$example4 = new ItemsConvertToNewArrayWithFunctionNotReferencePass($examples_array);
$itm4 = & $example4->getArrayItemReferences()[2];
$itm4 = null;unset($itm4);
echo '<pre>';
var_dump($example1,$example2,$example3,$example4);
echo '</pre>';