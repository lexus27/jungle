<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.04.2016
 * Time: 22:29
 */
namespace Jungle\RegExp {

	use Jungle\RegExp\Template\Placeholder;

	/**
	 * Class Template
	 * @package Jungle\RegExp
	 *
	 *
	 * Шаблонизирование регулярных выражений:
	 * /users/{user_id:int:(\d+)}
	 *
	 */
	class Template{

		protected static $template_regex = '@\\{(\w[\w\d]*)(?::(\w[\w\d]*))?(?::\\((.*?)\\))?\\}@';

		protected $definition;


		protected $compiled_template;

		protected $compiled_matcher;


		protected $placeholders = [];

		public function __construct($template){
			$this->definition = $template;


			if(preg_match_all(self::$template_regex,$template, $m, PREG_OFFSET_CAPTURE)){
				$compiler = addcslashes($template,'\'');
				foreach(array_reverse($m[0],true) as $i => list($match,$start_pos)){
					$len = strlen($match);
					$before = substr($template,0,$start_pos);
					$after = substr($template,$start_pos+$len);
					$name = $m[1][$i][0];
					$compiler = $before . '\' . $'.$name.' . \'' . $after;
				}
			}




		}

		protected function _addPlaceholder($position, Placeholder $placeholder){

			$this->placeholders[$position] = $placeholder;

		}


	}
}

