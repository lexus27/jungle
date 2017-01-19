<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.01.2017
 * Time: 2:27
 */
namespace Jungle\Util {

	/**
	 * Class IdentifierScope
	 * @package Jungle\Util
	 * @TITLE
	 * Генератор идентификаторов
	 *
	 * @DESCRIPTION
	 * Несколько генераторов будут отдавать разные идентификаторы по одинаковому $name
	 * При генерации страницы, идентификаторы всегда будут отличатся от предыдущих генераций
	 *
	 * @EXAMPLE
	 * <?php
	 *      $scope = new IdentifierScope();
	 *      //$scope->name = 'form';
	 *      if($scope->id('my_id') === $scope->id('my_id')){
	 *          // always true
	 *      }
	 * ?>
	 *
	 * <div id="<?=$scope->id('my_id')?>">
	 * <script>
	 *      window.onload = function(){
	 *          $('<?=$scope->id('my_id')?>').click(function(){
	 *              // my handler
	 *          });
	 *      };
	 * </script>
	 *
	 * @DESCRIPTION
	 * НО Если, придумать генерацию идентификатора не на базе уникального значения, а на особенностях контекста в котором он
	 * вычисляется, то каждый ответ будет содержать те же идентификаторы в разных контекстах, что и в предыдущих ответах.
	 */
	class IdentifierScope{

		public $name;

		public $carriers = [];

		/**
		 * @param $name
		 * @return mixed
		 */
		public function id($name){
			if(!isset($this->carriers[$name])){
				if($this->name){
					$id = $this->name.'_'.$name;
				}else{
					$id = System::uniqSysId($name.'_');
				}
				$this->carriers[$name] = $id;
				return $id;
			}
			return $this->carriers[$name];
		}

	}
}

