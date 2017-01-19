<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.12.2016
 * Time: 16:00
 */
namespace Jungle\Data\Record\RelationFS {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Relation\Relation;
	use Jungle\Data\Record\Schema\Schema;
	use Jungle\FileSystem\Model\Directory;
	use Jungle\FileSystem\Model\File;
	use Jungle\RegExp\Template;

	/**
	 * Class RelationFileSystem
	 * @package Jungle\Data\Record\Relation
	 */
	abstract class RelationFileSystem extends Relation{

		/** @var  string|null */
		public $field;

		/** @var Directory|RelationDir */
		public $source;

		/** @var bool  */
		public $monopoly = true;

		/** @var bool  */
		public $auto_create = false;

		/** @var Template @auto-generate */
		public $template;


		/**
		 * RelationFile constructor.
		 * @param $name
		 * @param RelationDir|Directory|string $source
		 * @param $field
		 * @param bool $monopoly
		 * @param bool $auto_create
		 */
		public function __construct($name, $source, $field = null, $monopoly = true, $auto_create = false){
			$this->name = $name;
			$this->source = $source;
			$this->field = $field;
			$this->monopoly = $monopoly;
			$this->auto_create = $auto_create;
		}


		/**
		 * @param $template
		 * @param null $template_ph_opts
		 * @param null $template_opts
		 * @return $this
		 */
		public function template($template, $template_ph_opts = null, $template_opts = null){
			if(!$template instanceof Template){
				$template = new Template($template, null, $template_ph_opts, $template_opts);
			}
			$this->template = $template;
			return $this;
		}

		/**
		 * @param Record $record
		 * @return null
		 * @throws Template\Exception
		 */
		public function generatePath(Record $record){
			$tpl = $this->template;
			$data = $record->getProperties($tpl->getPlaceholderNames());
			return $tpl->render($data)?:null;
		}


		/**
		 * @return array
		 */
		public function getInvolvedFields(){
			return $this->template->getPlaceholderNames();
		}


		/**
		 * @param Record $record
		 * @return Directory|null
		 * @throws \Exception
		 */
		public function getSourceIn(Record $record){
			/** @var Directory|RelationDir $source */
			$source = $this->source;
			if($source instanceof RelationDir){
				$source = $record->getRelated($source->name);
			}
			return $source;
		}

		public function initialize(Schema $schema){
			if(!$this->source){
				throw new \LogicException(__CLASS__ . ': must be set referenced_schema to system base directory as {source}');
			}

			$source = $this->source;
			if(!$source instanceof Directory){
				if(is_string($source)){
					$dir = $this->schema->getRepository()->getDirectory($source);
					if(!$dir instanceof Directory){
						throw new \LogicException('Not found system base directory by "'.$source.'"');
					}
					$source = $dir;
				}elseif(!$source instanceof RelationDir){
					throw new \LogicException('Source must be string base directory key OR "RelationDir" instance');
				}
			}
			$this->source = $source;
		}


		/**
		 * @return mixed
		 */
		public function getFieldName(){
			return $this->field;
		}

		/**
		 * @return array
		 */
		public function getLocalFields(){
			return [$this->field];
		}


		/**
		 * @param Record $record
		 */
		public function afterRecordDelete(Record $record){
			if($this->monopoly){
				/** @var File|Directory $related */
				$related = $record->getRelated($this->name);
				if($related){
					$related->delete(true);
				}
			}
		}


		/**
		 * @param Record $record
		 * @return mixed
		 */
		abstract function fetchPathIn(Record $record);

	}
}

