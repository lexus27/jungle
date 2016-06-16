<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 16.05.2016
 * Time: 13:07
 */
$origin = ['id','name','city'];
$whiteList = ['name','city'];
$readOnly = ['id'];
$private = ['name'];
$destination = array_intersect($origin,$whiteList);
$destination = array_diff($destination, $readOnly);
$destination = array_diff($destination, $private);
echo '<pre>';var_dump($destination);echo '</pre>';