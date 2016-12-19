<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 16.12.2016
 * Time: 23:41
 */
namespace Jungle\Data\Record\RelationFS {

	use Jungle\Data\Record;
	use Jungle\FileSystem\Model\Directory;
	use Jungle\RegExp\Template;

	/**
	 * @Source
	 * Class RelationDir
	 * @package Jungle\Data\Record\Relation
	 *
	 *
	 * load by path;
	 *
	 * {source/} {path} /
	 *
	 *
	 * Путь к DIR относительно {source}
	 * Формируется по шаблону
	 * Результат Выставляется в локальное поле которое семантично только данной папке
	 * Или Поле отсутствует [Постоянная папка]
	 *
	 *
	 *
	 * Если нет локального поля: При изменении полей объекта, которые используются в шаблоне,
	 * старая папка под старым путем передислоцируется и переименуется в новый
	 * снова сформированный путь
	 */
	class RelationDir extends RelationFileSystem{


		/**
		 * @param Record $record
		 * @return Directory|null
		 */
		public function load(Record $record){
			$source = $this->getSourceIn($record);
			if(!$source){
				return null;
			}
			$directory = null;
			$path = $this->fetchPathIn($record);
			if($path){
				$directory = $source->get($path);
			}

			if($this->auto_create && !$directory){
				if($record->getRecordState() === $record::STATE_LOADED){
					if($path){
						return $source->dir($path);
					}
				}else{
					return $source->getManager()->newDir('tmp_directory');
				}
			}
			return $directory;
		}

		/**
		 * @param Record $record
		 * @return string
		 * @throws \Exception
		 */
		public function fetchPathIn(Record $record){
			$path = null;
			if(!$this->field){
				return $this->generatePath($record);
			}
			return $record->getProperty($this->field);
		}

		/**
		 * @param Record $record
		 */
		public function afterRecordSave(Record $record){
			/** @var Directory $related */
			$involved_fields = $this->getInvolvedFields();
			$related = $record->getRelated($this->name);
			if(
				(
					$related &&
					$record->hasChangesRelated($this->name)
				) || (
					!$this->field &&
					$involved_fields &&
					$record->hasChangesProperty($involved_fields)
				)
			){
				$source = $this->getSourceIn($record);
				$new_path = $this->fetchPathIn($record);
				$related->renameFrom($new_path, $source);
				if($this->field!==null){
					$record->setProperty($this->field, $new_path);
				}
			}elseif(!$related){
				$source = $this->getSourceIn($record);
				$new_path = $this->fetchPathIn($record);
				$directory = $source->dir($new_path);
				if($this->field!==null){
					$record->setProperty($this->field, $new_path);
				}
				$record->setRelated($this->name, $directory);
			}elseif($related){
				if($this->field!==null){
					$source = $this->getSourceIn($record);
					$record->setProperty($this->field, $related->getRelativePath($source));
				}
			}
		}


	}
}

