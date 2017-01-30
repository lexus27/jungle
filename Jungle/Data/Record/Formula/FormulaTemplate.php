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
	use Jungle\Data\Record\Schema\Schema;
	use Jungle\RegExp\Template;

	/**
	 * Class FormulaTemplate
	 * @package Jungle\Data\Record\Formula
	 */
	class FormulaTemplate extends Formula{

		/** @var  string */
		protected $template;

		/** @var  bool */
		protected $track_involved_change;

		/**
		 * FormulaTemplate constructor.
		 * @param $field
		 * @param Template|string $template
		 * @param bool|false $empty_check
		 * @param array $track_involved_change
		 */
		public function __construct($field, $template, $empty_check = false,array $track_involved_change = []){
			$this->field        = $field;
			$this->empty_collate  = $empty_check;
			if(!$template instanceof Template){
				$this->template = new Template($this->template);
			}else{
				$this->template = $template;
			}

			$this->track_involved_change = $track_involved_change;
		}

		public function check(Record $record, $op_made){
			$_ = parent::check($record, $op_made);
			return $_ || ($this->track_involved_change && $record->hasChangesProperty($this->getTrackInvolvedPaths()));
		}


		/**
		 * @param Record $record
		 * @return mixed
		 */
		public function fetch(Record $record){
			$tpl = $this->template;
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
			return $this->template->getPlaceholderNames();
		}

		public function getTrackInvolvedPaths(){
			return array_intersect($this->track_involved_change, $this->getInvolvedFields());
		}

		/**
		 * @param Schema $schema
		 */
		public function attachToSchema(Schema $schema){
			foreach($this->getTrackInvolvedPaths() as $path){

				if($pos = strpos($path,'::')!==false){

					$extra_query = substr($path, 0, $pos+1);

				}

			}
		}

	}
}

