<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.12.2016
 * Time: 15:09
 */
namespace Jungle\Data\Record\Formula {
	
	use Jungle\Data\Record;
	use Jungle\RegExp\Template;

	/**
	 * Class FormulaTemplate
	 * @package Jungle\Data\Record\Formula
	 */
	class FormulaTemplate extends Formula{

		/** @var  string */
		protected $template;

		/** @var  Template */
		protected $template_compiled;

		/** @var  bool */
		protected $track_involved_change;

		/**
		 * FormulaTemplate constructor.
		 * @param $field
		 * @param bool|false $template
		 * @param bool|false $empty_check
		 * @param bool $track_involved_change
		 */
		public function __construct($field, $template, $empty_check = false, $track_involved_change = false){
			$this->field        = $field;
			$this->empty_check  = $empty_check;
			$this->template     = $template;

			if($template instanceof Template){
				$this->template_compiled = $template;
				$this->template          = $template->getDefinition();
			}

			$this->track_involved_change = $track_involved_change;
		}

		public function check(Record $record, $op_made){
			$_ = parent::check($record, $op_made);
			return $_ || ($this->track_involved_change && $record->hasChangesProperty($this->getInvolvedFields()));
		}


		/**
		 * @param Record $record
		 * @return mixed
		 */
		public function fetch(Record $record){
			$tpl = $this->_compile();
			$data = [];
			foreach( $tpl->getPlaceholderNames() as $name){
				$data[$name] = $record->getProperty($name);
			}
			return $tpl->render($data);
		}

		/**
		 * @return array
		 */
		public function getInvolvedFields(){
			return $this->_compile()->getPlaceholderNames();
		}

		/**
		 * @return Template
		 */
		protected function _compile(){
			if(!$this->template_compiled){
				$this->template_compiled = new Template($this->template,Template\Manager::getDefault());
			}
			return $this->template_compiled;
		}

	}
}

