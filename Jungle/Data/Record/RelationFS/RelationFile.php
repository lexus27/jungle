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
	use Jungle\Data\Record\Schema\Schema;
	use Jungle\Data\Record\Snapshot;
	use Jungle\FileSystem\Model\Directory;
	use Jungle\FileSystem\Model\File;
	use Jungle\Http\UploadedFile;
	use Jungle\Util\Replacer\Replacer;
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
			//$tpl = $this->template;
			if($this->template){
				$replacer = new Replacer('{','}','\w+(?:\.+\w+)*(?::\w+(?:\.+\w+)*)*');
				return $replacer->replace($this->template->getDefinition(),function($placeholder) use($record, $file){
					$result = '';
					$modifiers = explode(':',ltrim(strstr($placeholder,':'),':') );
					$placeholder = strstr($placeholder,':',true);
					if(strpos($placeholder,'@')===0){
						$placeholder = ltrim($placeholder,'@');
						switch($placeholder){
							case 'basename': $result = $file->getBasename();break;
							case 'name':$result =  pathinfo($file->getBasename(),PATHINFO_FILENAME);break;
							case 'name.ext':$result =  pathinfo($file->getBasename(),PATHINFO_EXTENSION);break;
							case 'param': $result =  $file->param_name; break;
							case 'media_type':$result = $file->getMediaType(); break;
							case 'ext':$result = trim(strstr($file->getMediaType(),'/',false),'/');break;
							case 'type':$result = trim(strstr($file->getMediaType(),'/',true),'/');break;
							case 'size':$result = $file->getSize(); break;
						}
					}else{
						$result = $record->getProperty($placeholder);
					}
					foreach($modifiers as $modifier){
						$result = call_user_func($modifier, $result);
					}
					return $result;
					
				});
			}else{
				
			}
		}
		
		
		
		
		
		
		public function isVariableTemplate(){
			if($this->template){
				$ph = $this->template->getPlaceholderNames();
				return !!Massive::getCovered($ph,false,'@');
			}else{
				return false;
			}
		}
		
		
		
		/**
		 * @param Record $record
		 * @param Snapshot $snapshot
		 * @throws \Exception
		 * @throws \Jungle\FileSystem\Model\Exception\ActionError
		 */
		public function beforeRecordSave(Record $record, Snapshot $snapshot = null){
			
			/**
			 * Обработка принятого файла UploadedFile
			 * Работа с файлом который уже загружен - File
			 */
			
			$this->_path_after = false;
			try{
				if(!$this->field && $this->type === self::TYPE_UPLOAD_AUTOGEN
				   && ($involved_fields = $this->getInvolvedFields())
				   && $record->hasChangesProperty($involved_fields)
				){ // автогенерация-имени файла из полей объекта
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
					// контроль пути к файлу по какому-то полю объекта
					$related = $record->getRelated($this->name);
					$source = $this->getSourceIn($record);
					if(in_array($this->type,[self::TYPE_UPLOAD, self::TYPE_UPLOAD_AUTOGEN], true)){
						// тип: Поле для Загрузки файла, подключение файла из вне
						if($related instanceof UploadedFile){
							// TODO папка назначения для файла может задаваться с клиента.
							$new_path = $this->_call_event_accepted($record, $related);
							if($record->getOperationMade() === Record::OP_CREATE){
								if($this->_on_path){
									$this->_path_after = [
										'source' => $source,
										'file' => $related,
									];
									if($new_path){
										// Фикс для валидации перед сохранением
										if($this->field){
											$record->setProperty($this->field, $new_path);
										}
									}
								}else{
									if($new_path === null){
										// здесь путь назначения до файла указывается объектом ORM через шаблон
										$new_path = $this->fetchPathForUploaded($record, $related);
									}
									$this->_applyPath($record, $new_path, $related, $source);
								}
							}else{
								if($this->_on_path){
									$p = $this->_call_event_path($record, $related);
									if($p === null){
										if($new_path === null){
											// здесь путь назначения до файла указывается объектом ORM через шаблон
											$new_path = $this->fetchPathForUploaded($record, $related);
										}
									}else{
										$new_path = $p;
									}
								}
								$oldPath = $record->getSnapshot()->get($this->field);
								$this->_applyPath($record, $new_path, $related, $source, $oldPath?:null);
							}
						}elseif($related === null){
							$snapshot = $record->getRelatedSnapshot();
							$old = $snapshot->get($this->name);
							if($old instanceof File){
								$old->delete(true);
							}
						}
					}elseif($this->type === self::TYPE_SELECTED){
						// тип: Выбираемый файл относительно SOURCE
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
			}catch(\Exception $e){
				$this->_path_after = false;
				throw $e;
			}
			
		}
		
		protected function _applyPath(Record $record, $path, UploadedFile $file, Directory $source, $oldPath = null){
			$b_name = basename($path);
			$directory = $source->dir(dirname($path));
			if($old_file = $directory->get($b_name)){
				$old_file->delete(true);
			}
			if($oldPath && ($dir = $source->dir(dirname($oldPath))) && ($old_file = $dir->get(basename($oldPath)))){
				$old_file->delete(true);
			}
			// переместить UploadedFile на новый путь, при этом используя абсолютный путь в системе
			$file->moveTo($source->getAbsolutePath(null,$path));
			$related_file = $directory->get($b_name);
			// Выставить новый объект
			$record->setRelated($this->name,$related_file);
			if($this->field){
				$record->setProperty($this->field, $path);
			}
		}
		
		public function afterRecordSave(Record $record, Snapshot $snapshot = null){
			try{
				if($this->_path_after && isset($this->_path_after['file'], $this->_path_after['source'])){
					$file = $this->_path_after['file'];
					$source = $this->_path_after['source'];
					if($file instanceof UploadedFile && $source){
						$path = $this->_call_event_path($record, $file);
						$this->_applyPath($record, $path, $file, $source);
						$record->saveAgain();
					}
				}
			}finally{
				$this->_path_after = false;
			}
		}
		
		
		public function initialize(Schema $schema){
			
			if($this->type === self::TYPE_UPLOAD || $this->type === self::TYPE_SELECTED){
				if(!$this->field){
					throw new \LogicException('RelationFile(TYPE_UPLOAD | TYPE_SELECTABLE) bad definition!: local field not be specified');
				}
			}elseif($this->type === self::TYPE_UPLOAD_AUTOGEN){
				if(!$this->_on_path && !$this->_on_accepted && !$this->template){
					throw new \LogicException('RelationFile(TYPE_UPLOAD_AUTOGEN) bad definition!: Template not be specified');
				}
			}
			
			parent::initialize($schema);
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
		
		protected $_on_accepted;
		protected $_on_path;
		protected $_path_after = false;
		
		/**
		 * @param Record $record
		 * @param UploadedFile $file
		 * @return mixed|null
		 */
		protected function _call_event_accepted(Record $record, UploadedFile $file){
			if($this->_on_accepted){
				if(is_callable($this->_on_accepted)){
					return call_user_func($this->_on_accepted, $record, $file, $this);
				}elseif(is_array($this->_on_accepted)){
					$handler = $this->_on_accepted;
					$method_name = $handler[0];
					array_shift($handler);
					array_unshift($handler, $this);
					array_unshift($handler, $file);
					return call_user_func_array([$record, $method_name],$handler);
				}else{
					return call_user_func([$record, $this->_on_accepted],$file, $this);
				}
				
			}
			return null;
		}
		
		/**
		 * @param Record $record
		 * @param UploadedFile $file
		 * @return mixed|null
		 */
		protected function _call_event_path(Record $record, UploadedFile $file){
			if($this->_on_path){
				if(is_callable($this->_on_path)){
					return call_user_func($this->_on_path, $record, $file, $this);
				}elseif(is_array($this->_on_path)){
					$handler = $this->_on_path;
					$method_name = $handler[0];
					array_shift($handler);
					array_unshift($handler, $this);
					array_unshift($handler, $file);
					return call_user_func_array([$record, $method_name],$handler);
				}else{
					return call_user_func([$record, $this->_on_path],$file, $this);
				}
				
			}
			return null;
		}
		
		public function setAccepted($handler){
			$this->_on_accepted = $handler;
			return $this;
		}
		
		public function setPath($handler){
			$this->_on_path = $handler;
			return $this;
		}
	}
}

