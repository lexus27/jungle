<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 13.12.2016
 * Time: 15:28
 */
namespace Jungle\Data\Record\RelationFS {
	
	use Jungle\Data\Record;
	use Jungle\Data\Record\Relation\Relationship;
	use Jungle\FileSystem\Model\File;
	use Jungle\Http\UploadedFile;
	use Jungle\RegExp\Template;
	use Jungle\Util\Value\Massive;

	/**
	 * Class RelationFile
	 * @package Jungle\Data\Record\Relation
	 */
	class RelationFile extends RelationFileSystem{

		const PLACEHOLDER_UPLOADED_PREFIX = '~';

		const TYPE_UPLOAD         = 'upload';
		const TYPE_UPLOAD_AUTOGEN = 'upload-autogen';
		const TYPE_SELECTED       = 'selected';

		public $type = self::TYPE_UPLOAD;


		/**
		 * @param Record $record
		 * @return File|null
		 */
		public function load(Record $record){
			$source = $this->getSourceIn($record);
			$file = null;
			if($source){
				$path = $this->fetchPathIn($record);
				if($path){
					$file = $source->get($path);
					// если файла нет ;( и если $path был получен
					// при этом автоматического создания происходить не должно
					// то бъем тревогу, автоматически убирая файл и выставляя поле в NULL
					if(!$file && !$this->auto_create && $this->field){
						$record->setProperty($this->field, null);
					}
					return $file;
				}
			}
			if($this->auto_create){
				return $source->getManager()->newFile('tmp_file');
			}
			return null;
		}

		/**
		 * @param Record $record
		 * @return Record|\Jungle\Data\Record[]|Relationship|mixed
		 * @throws \Exception
		 */
		public function fetchPathIn(Record $record){
			if($this->field){
				$val = $record->getProperty($this->field);
				if(!$val && $this->type === self::TYPE_UPLOAD_AUTOGEN){
					return null;
				}
				return $val;
			}
			if($this->type === self::TYPE_UPLOAD_AUTOGEN){
				return $this->generatePath($record);
			}
			return null;
		}

		/**
		 * @param Record $record
		 * @param UploadedFile $uploaded
		 * @return null|string
		 */
		public function fetchPathForUploaded(Record $record, UploadedFile $uploaded){
			if($this->type === self::TYPE_UPLOAD_AUTOGEN){
				return $this->generateNewPath($record, $uploaded);
			}else{
				// Не хватает директории в которую нужно поместить файл,
				// это уже дополнительный опрос SOURCE на существование директории
				return $uploaded->getBasename();
			}
		}

		/**
		 * @param Record $record
		 * @param UploadedFile $file
		 * @return null
		 */
		public function generateNewPath(Record $record, UploadedFile $file){
			$tpl = $this->template;

			$uploaded_data['basename'] = $file->getBasename();
			$uploaded_data['name'] = pathinfo($uploaded_data['basename'],PATHINFO_FILENAME);
			$uploaded_data['name_ext'] = pathinfo($uploaded_data['basename'],PATHINFO_EXTENSION);
			$uploaded_data['param'] = $file->param_name;

			$uploaded_data['media_type'] = $file->getMediaType();
			$uploaded_data['type'] = trim(strstr($uploaded_data['media_type'],'/',true),'/');
			$uploaded_data['ext']  = trim(strstr($uploaded_data['media_type'],'/',false),'/');
			$uploaded_data['size'] = $file->getSize();

			// prefixed
			$uploaded_data = Massive::cover($uploaded_data,self::PLACEHOLDER_UPLOADED_PREFIX);

			$record_data = $record->getProperties($tpl->getPlaceholderNames());

			$data = array_replace($record_data,$uploaded_data);

			return $tpl->render($data)?:null;
		}






		public function isVariableTemplate(){
			if($this->template){
				$ph = $this->template->getPlaceholderNames();
				return !!Massive::getCovered($ph,false,'@');
			}else{
				return false;
			}
		}


		public function afterRecordSave(Record $record){

			/**
			 * Обработка принятого файла UploadedFile
			 * Работа с файлом который уже загружен - File
			 */


			if(!$this->field && $this->type === self::TYPE_UPLOAD_AUTOGEN
			   && ($involved_fields = $this->getInvolvedFields())
			   && $record->hasChangesProperty($involved_fields)
			){
				/** @var File|null $related */
				$related = $record->getRelatedLoaded($this->name);
				$source = $this->getSourceIn($record);
				$new_path = $this->fetchPathIn($record);
				$b_name = basename($new_path);
				$directory = $source->dir(dirname($new_path));
				if($file = $directory->get($b_name)){
					$file->delete(true);
				}
				if($related){
					$related->renameFrom($new_path, $source);
				}
			}elseif($record->hasChangesRelated($this->name)){
				$related = $record->getRelated($this->name);
				$source = $this->getSourceIn($record);
				if(in_array($this->type,[self::TYPE_UPLOAD, self::TYPE_UPLOAD_AUTOGEN], true)){
					if($related instanceof UploadedFile){
						// TODO папка назначения для файла может задаваться с клиента.
						// здесь путь назначения до фалйа указывается объектом ORM через шаблон
						$new_path = $this->fetchPathForUploaded($record,$related);
						$b_name = basename($new_path);
						$directory = $source->dir(dirname($new_path));
						if($file = $directory->get($b_name)){
							$file->delete(true);
						}
						// переместить UploadedFile на новый путь, при этом используя абсолютный путь в системе
						$related->moveTo($source->getAbsolutePath(null,$new_path));
						// Выставить новый объект
						$record->setRelated($this->name,$directory->get($b_name));
						if($this->field){
							$record->setProperty($this->field, $new_path);
						}
					}elseif($related === null){
						$snapshot = $record->getRelatedSnapshot();
						$old = $snapshot->get($this->name);
						if($old instanceof File){
							$old->delete(true);
						}
					}
				}elseif($this->type === self::TYPE_SELECTED){
					if($related instanceof File){
						if(!$source->isContain($related)){
							throw new \LogicException('Related selected File is not exists in Source('.$source->getRealPath().')');
						}
						$record->setProperty($this->field, $related->getRelativePath($source));
					}
				}

				if($related === null && $this->field){
					$record->setProperty($this->field,null);
				}

			}
		}

		public function initialize(){

			if($this->type === self::TYPE_UPLOAD || $this->type === self::TYPE_SELECTED){
				if(!$this->field){
					throw new \LogicException('RelationFile(TYPE_UPLOAD | TYPE_SELECTABLE) bad definition!: local field not be specified');
				}
			}elseif($this->type === self::TYPE_UPLOAD_AUTOGEN){
				if(!$this->template){
					throw new \LogicException('RelationFile(TYPE_UPLOAD_AUTOGEN) bad definition!: Template not be specified');
				}
			}

			parent::initialize();
		}

		/**
		 * @param Record $record
		 * @throws \Exception
		 * @throws \Jungle\FileSystem\Model\Exception\ActionError
		 */
		public function afterRecordDelete(Record $record){
			if($this->monopoly || $this->type === self::TYPE_UPLOAD_AUTOGEN){
				/** @var File $related */
				$related = $record->getRelated($this->name);
				if($related){
					$related->delete(true);
				}
			}
		}

	}
}

