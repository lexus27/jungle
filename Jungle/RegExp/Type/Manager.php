<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.04.2016
 * Time: 19:00
 */
namespace Jungle\RegExp\Type {

	use Jungle\Exception;
	use Jungle\RegExp;
	use Jungle\RegExp\Type;
	use Jungle\Util\Value;

	/**
	 * Class Manager
	 * @package Jungle\RegExp
	 */
	class Manager implements TypeAwareInterface{

		use TypeAggregateTrait{
			getType as protected _getType;
		}

		/** @var  Manager */
		protected static $_default;

		/** @var  Manager|null */
		protected $parent;

		/** @var  bool  */
		protected $parent_overlap = false;

		/**
		 * @param $name
		 * @return Type|null
		 */
		public function getType($name){
			if($this->parent_overlap && ($type = $this->parent->getType($name))!==null){
				return $type;
			}
			$type =  $this->_getType($name);
			if($type){
				return $type;
			}
			if(!$this->parent_overlap && $this->parent){
				return $this->parent->getType($name);
			}
			return null;
		}

		/**
		 * @param Manager|null $parent
		 * @param bool $overlap
		 * @return $this
		 */
		public function setParent(Manager $parent = null, $overlap = false){
			$this->parent = $parent;
			$this->parent_overlap = $overlap;
			return $this;
		}

		/**
		 * @return Manager|null
		 */
		public function getParent(){
			return $this->parent;
		}


		/**
		 * @return static
		 */
		public function extend(){
			$set = new static();
			$set->setParent($this,false);
			return $set;
		}

		/**
		 * @return static
		 */
		public function overlap(){
			$set = new static();
			$set->setParent($this,true);
			return $set;
		}

		/**
		 * @param Type $type
		 */
		protected function afterCreate(Type $type){
			$type->setRegistry($this);
		}

		/**
		 * @param $value
		 * @param $type
		 * @param array $options
		 * @return bool
		 * @throws Exception
		 */
		public static function validate($value, $type, array $options = null){
			$manager = static::getDefault();
			$t = $manager->getType($type);
			if($t){
				return $t->validate($value, $options);
			}
			throw new Exception('RegExp.Type "'.$type.'" not found in manager!');
		}

		/**
		 * @return Manager
		 */
		public static function getDefault(){
			if(!self::$_default){
				$r = new Manager();
				$r->add(['string','any'])
						->setVartype('string')
						->setPattern('.+');

				$r->add('word')
						->setVartype('string')
						->setPattern('\w[\w\d\s]*');
				$r->add('keyword')
					->setVartype('string')
					->setPattern('\w[\w\-\s]*)');
				$r->add('key')
					->setVartype('string')
					->setPattern('\w[\w\-\.\s]*');
				$r->add('text')
						->setVartype('string')
						->setPattern('[[:alpha:]][\w\s,\.\;\-\=\"\'\(\)\^\:\?\!]*)');

				/**
				 * Date
				 */
				$date = $r->addContainer('date')
						->setArgumentsSupport([
							'pattern:string:null'
						])
						->setPattern(function(Type $type, $pattern){
							if($pattern){
								return preg_replace_callback('@\[\#(&?[[:alpha:]][\w\-\.]*)\#\]@S',function($m) use($type){
									return $type->getSubPattern($m[1]);
								},$pattern);
							}else{
								return '[\d]{4,5}-[\d]{1,2}-[\d]{1,2}';
							}
						});

				$date->add('time24')->setPattern('(?:2[0-4]|1?[0-9]):(?:[1-5]?[0-9]|60):(?:[1-5]?[0-9]|60)');
				$date->add('time24-minutes')->setPattern('(?:2[0-4]|1?[0-9]):(?:[1-5]?[0-9]|60)');
				$date->add('time12')->setPattern('(?:1[0-2]|[0-9]):(?:[1-5]?[0-9]|60):(?:[1-5]?[0-9]|60) (?i:am|pm)');
				$date->add('time12-minutes')->setPattern('(?:1[0-2]|[0-9]):(?:[1-5]?[0-9]|60) (?i:am|pm)');

				$moth = $date->addContainer('moth')->setPattern('(?i:january|february|mart|april|may|june|july|august|september|october|november|december)');
				$moth->add('short')->setPattern('(?i:jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)');

				$weekday = $date->addContainer('weekday')->setPattern('(?i:monday|tuesday|wednesday|thursday|friday|saturday|sunday)');
				$weekday->add('short')->setPattern('(?i:mon|tue|wed|thu|fri|sat|sun)');

				$r->add('email')
						->setVartype('string')
						->setPattern('[[:alpha:]][\w\-\_]*@[[:alpha:]]\w*\.[[:alpha:]]\w*');

				$r->add('range')
					->setArgumentsSupport([
						'range		:mixed',
						'vartype	:string		:string',
						'caseless	:boolean	:true',
						'separator	:string		:|'
					])
					->setPattern(function(Type $type, $range,$vartype,$caseless, $separator){
						if(!is_array($range))$range = explode($separator, $range);
						return '(?'.($caseless?'i':'').':'.implode('|',RegExp::pregQuoteArray($range,'@')).')';
					})
					->setEvaluator(function($value, Type $type, $range,$vartype,$caseless, $separator){
						return Value::setVartype($value, $vartype);
					});

				/**
				 * Integer
				 */
				$intCt = $r->addContainer(['integer','int'])
						->setVartype('integer')
						->setPattern('[+\-]?(?:[1-9]\d*|0)');
				$intCt->add('nozero')->setVartype('integer')->setPattern('[+\-]?[1-9]\d*');
				$intCt->add('unsigned')->setVartype('integer')->setPattern('(?:[1-9]\d*|0)');
				$intCt->add('unsigned-nozero')->setVartype('integer')->setPattern('[1-9]\d*');
				$intCt->add('nozero')->setVartype('integer')->setPattern('[+\-]?[1-9]\d*(?:\.\d+)?');
				$intCt->add('unsigned-nozero')->setVartype('integer')->setPattern('[1-9]\d*(?:\.\d+)?');

				/**
				 * Float
				 */
				$floatCt = $r->addContainer(['float','double','number','numeric'])
						->setVartype('double')
						->setPattern('[+\-]?(?:[1-9]\d*|0)(?:\.\d+)?');

				$floatCt->add('unsigned')->setVartype('double')->setPattern('(?:[1-9]\d*|0)(?:\.\d+)?');


				/**
				 * Arrays
				 */
				$arrCt = $r->addContainer(['array','list'])
						->setVartype('array')
						->setArgumentsSupport([
								'delimiter      :string     :,',
								'item_pattern   :string     :null',
								'item_vartype   :string     :null',
								'limit          :integer    :-1',
								'allow_empty    :boolean    :false',
								'item_default   :mixed      :null',
						])
						->setPattern(function(Type $type,$delimiter, $item_pattern, $item_vartype, $limit, $allow_empty, $item_default){
							$delimiter = preg_quote($delimiter,'@');
							if(!$item_pattern){
								$item_pattern = '[^'.$delimiter.']'.($allow_empty?'*?':'+?');
							}elseif(substr($item_pattern,0,2)==='[#' && substr($item_pattern,-2)==='#]'){
								$item_pattern = '(?:'.$type->getSubPattern(substr($item_pattern,2,-2)).')'.($allow_empty?'?':'');
							}
							$item_pattern = '(?:'.$item_pattern.')';
							if($limit === 1){
								return $item_pattern;
							}else{
								return $item_pattern . '(?:'.$delimiter.$item_pattern.')' . ($limit===-1?'*?':'{0,'.($limit-1).'}');
							}
						})
						->setEvaluator(function($value, Type $type,$delimiter, $item_pattern, $item_vartype, $limit, $allow_empty, $item_default){
							$items = explode($delimiter, $value);
							if(substr($item_pattern,0,2)==='[#' && substr($item_pattern,-2)==='#]' && !$item_vartype){
								$item_vartype = $type->getSubVartype(substr($item_pattern,2,-2));
							}
							if(!$allow_empty){
								$items = array_filter($items);
							}
							if($item_vartype){
								foreach($items as $i => $itm){
									if($allow_empty && $itm==''){
										$items[$i] = $item_default;
									}else{
										Value::settype($itm,$item_vartype);
										$items[$i] = $itm;
									}
								}
							}
							return $items;
						})
						->setRenderer(function(array $value,Type $type,$delimiter, $item_pattern, $item_vartype, $limit, $allow_empty, $item_default){
							if(!$allow_empty){
								$value = array_filter($value);
							}
							$cnt = count($value);
							if($limit>0 && $cnt > $limit){
								throw new \LogicException('Type is limited: passed value count is long than "'.$limit.'"');
							}
							return implode($delimiter,$value);
						});
				$arrCt->add('fixed')
						->setVartype('array')
						->setArgumentsSupport([
								'count          :integer    :1',
								'delimiter      :string     :,',
								'item_pattern   :string     :null',
								'item_vartype   :string     :null',
								'allow_empty    :boolean    :false',
								'item_default   :mixed      :null',
						])
						->setPattern(function(Type $type, $count,  $delimiter, $item_pattern, $item_vartype, $allow_empty,$item_default){
							$delimiter = preg_quote($delimiter,'@');
							if(!$item_pattern){
								$item_pattern = '[^'.$delimiter.']'.($allow_empty?'*?':'+?');
							}elseif(substr($item_pattern,0,2)==='[#' && substr($item_pattern,-2)==='#]'){
								$item_pattern = '(?:'.$type->getSubPattern(substr($item_pattern,2,-2)).')'.($allow_empty?'?':'');
							}
							$item_pattern = '(?:'.$item_pattern.')';
							if($count === 1){
								return $item_pattern;
							}else{
								return $item_pattern . '(?:'.$delimiter.$item_pattern.')' . '{'.($count-1).'}';
							}
						})
						->setEvaluator(function($value, Type $type, $count,  $delimiter, $item_pattern, $item_vartype, $allow_empty,$item_default){
							$items = explode($delimiter, $value);
							if(substr($item_pattern,0,2)==='[#' && substr($item_pattern,-2)==='#]' && !$item_vartype){
								$item_vartype = $type->getSubVartype(substr($item_pattern,2,-2));
							}
							if(!$allow_empty){
								$items = array_filter($items);
							}
							if($count > 0 && $allow_empty && ($cnt = count($items)) < $count){
								while($cnt < $count){
									$items[] = null;
									$cnt++;
								}
							}
							if($item_vartype){
								foreach($items as $i => $itm){
									if($allow_empty && $itm==''){
										$items[$i] = $item_default;
									}else{
										Value::settype($itm,$item_vartype);
										$items[$i] = $itm;
									}
								}
							}
							return $items;
						})
						->setRenderer(function(array $value, Type $type, $count,  $delimiter, $item_pattern, $item_vartype, $allow_empty,$item_default){
							if(!$allow_empty){
								$value = array_filter($value);
							}
							$cnt = count($value);
							if($allow_empty && $cnt < $count){
								while($cnt < $count){
									$value[] = null;
									$cnt++;
								}
							}
							return implode($delimiter,$value);
						});
				/**
				 * Objects
				 */

				$r->add('object')
						->setVartype('array')
						->setArgumentsSupport([
								'separator      :string	:;',
								'assign_char    :string	:=',
								'key_pattern	:string	:null',
								'val_pattern	:string	:null',
								'val_vartype	:string	:null',
						])
						->setPattern(function(Type $type, $separator, $assign_char, $key_pattern, $val_pattern, $val_vartype){
							$separator = preg_quote($separator,'@');
							$assign_char = preg_quote($assign_char,'@');

							if(!$key_pattern){
								$key_pattern = '[[:alpha:]][\w\d]+';
							}
							if(!$val_pattern){
								$val_pattern = '[[:alpha:]][\w\d\.]+';
							}elseif(substr($val_pattern,0,2)==='[#' && substr($val_pattern,-2)==='#]'){
								$val_pattern = '(?:'.$type->getSubPattern(substr($val_pattern,2,-2)).')';
							}
							return '(?:'.$key_pattern.$assign_char.$val_pattern.')(?:(?:'.$separator.')(?:'.$key_pattern.$assign_char.$val_pattern.'))*';
						})
						->setEvaluator(function($value,Type $type, $separator, $assign_char, $key_pattern, $val_pattern, $val_vartype){
							$assign_char = preg_quote($assign_char,'@');
							if(!$key_pattern){
								$key_pattern = '[[:alpha:]]\w*';
							}
							if(!$val_pattern){
								$val_pattern = '[[:alpha:]][\w\.]*';
							}elseif(substr($val_pattern,0,2)==='[#' && substr($val_pattern,-2)==='#]'){
								list($p,$t) = $type->getSubType(substr($val_pattern,2,-2));
								$val_pattern = '(?:'.$p.')';
								if(!$val_vartype){
									$val_vartype = $t;
								}
							}

							if(!$value){
								return [];
							}
							if(preg_match_all('@('.$key_pattern.')' . $assign_char . '('.$val_pattern.')@S',$value, $results)){
								$data = [];
								foreach($results[0] as $index => $all){
									$param  = $results[1][$index];
									$val    = $val_vartype?Value::setVartype($results[2][$index],$val_vartype):$results[2][$index];
									$data[$param] = $val;
								}
								return $data;
							}else{
								throw new \LogicException('Error parse "object"');
							}
						})
						->setRenderer(function($value,Type $type,$separator, $assign_char, $key_pattern, $val_pattern, $val_vartype){
							$pairs = [];
							foreach($value as $property => $v){
								$v = (string)$v;
								if(strpos($v,$separator)){
									throw new \LogicException('Object property "'.$property.'" detect Assignment Separator in value('.$v.')');
								}
								if(strpos($property,$separator)){
									throw new \LogicException('Object property "'.$property.'" detect Assignment Separator in Property Key('.$property.')');
								}
								$pairs[] = $property . $assign_char . $v;
							}
							return implode($separator, $pairs);
						});

				$r->add(['bool','boolean'])
						->setVartype('boolean')
						->setArgumentsSupport([
								'true   :string  :yes|true|on|1',
								'false  :string  :no|false|off|0',
						])
						->setPattern(function(Type $type, $true, $false){
							$true   = array_map(function($v){return preg_quote($v,'@');},explode('|',$true));
							$false  = array_map(function($v){return preg_quote($v,'@');},explode('|',$false));
							return '(?i:'.implode('|',$true).'|'.implode('|',$false).')';
						})
						->setEvaluator(function($value,Type $type, $true, $false){
							$true = explode('|',$true);
							if(in_array($value, $true)){
								return true;
							}else{
								return false;
							}
						})
						->setRenderer(function($value,Type $type, $true, $false){
							$r = explode('|',$value ? $true: $false,1);
							return $r[0];
						});
				self::$_default = $r;
			}
			return self::$_default;
		}

		public static function setDefault(Manager $registry){
			self::$_default = $registry;
		}


	}
}

