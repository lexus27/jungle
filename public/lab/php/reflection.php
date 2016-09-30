<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 16.05.2016
 * Time: 15:05
 */
namespace php;

class MyClass{

	protected $id = 1;

}

$object= new MyClass();

$reflection = new \ReflectionClass('php\MyClass');


$id = $reflection->getProperty('id');
$id->setAccessible(true);


echo '<pre>';

$id->setValue($object,2);



echo $id->getValue($object) . "\r\n";



echo '</pre>';