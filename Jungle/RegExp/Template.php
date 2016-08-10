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

	use Jungle\RegExp\Template\Exception;
	use Jungle\RegExp\Template\Manager;
	use Jungle\RegExp\Template\Placeholder;

	/**
	 * Class Template
	 * @package Jungle\RegExp
	 *
	 *
	 * Шаблонизирование регулярных выражений:
	 * /user/{user.id:int}{extension?<.|>:word}{parameters?</|>:array(/)}
	 *
	 */
	class Template{

		/** @var  Manager */
		protected $manager;

		/** @var  string */
		protected $definition;

		/** @var  bool  */
		protected $compiled = false;

		/** @var  string */
		protected $regex;

		/** @var  Placeholder[]  */
		protected $placeholders = [];

		/** @var  array  */
		protected $placeholders_options = null;

		/** @var  array */
		protected $composite = [];

		/** @var array  */
		protected $options = [];


		/**
		 * Template constructor.
		 * @param $definition
		 * @param Manager $manager
		 * @param array $placeholders_options
		 * @param array $template_options
		 */
		public function __construct($definition,Manager $manager, array $placeholders_options = null, array $template_options = null){
			$this->definition = $definition;
			$this->placeholders_options = (array)$placeholders_options;
			$this->options = $template_options;
			$this->setManager($manager);
		}

		/**
		 * @return string
		 */
		public function getPlaceholderRegex(){
			return Manager::getPlaceholderRegex();
		}

		/**
		 * @return mixed
		 */
		public function getRegex(){
			if(!$this->compiled){
				$this->_compile();
			}
			return $this->regex;
		}

		/** @var  array|null  */
		protected $placeholder_names;

		/**
		 * @return array
		 */
		public function getPlaceholderNames(){
			if(null===$this->placeholder_names){
				$this->placeholder_names = [];
				foreach($this->getPlaceholders() as $ph){
					$this->placeholder_names[] = $ph;
				}
			}
			return $this->placeholder_names;
		}

		/**
		 * @return Placeholder[]
		 */
		public function getPlaceholders(){
			if(!$this->compiled){
				$this->_compile();
			}
			return $this->placeholders;
		}

		/**
		 * @param $name
		 * @return Placeholder|null
		 */
		public function getPlaceholder($name){
			$this->_compile();
			foreach($this->placeholders as $ph){
				if($ph->getName() === $name){
					return $ph;
				}
			}
			return null;
		}

		/**
		 * @param $name
		 * @param array $options
		 * @return Placeholder
		 */
		protected function makePlaceholder($name, array $options){
			$defaults = [
				'type'         		=> null,
				'arguments'    		=> [],
				'setArguments' 		=> [],
				'pattern'      		=> null,
				'evaluator'    		=> null,
				'renderer'     		=> null,
				'after'        		=> null,
				'before'       		=> null,
				'optional'     		=> false,
				'default'      		=> null,
				'default_render' 	=> true,
				'options'      		=> [],
			];
			if(($phOptions = $this->manager->getPlaceholderDefaults($name))){
				$defaults = array_replace($defaults, $phOptions);
			}
			$defaults = array_replace($defaults,$options);
			if(is_array($this->placeholders_options) && isset($this->placeholders_options[$name])){
				$defaults = array_replace($defaults, $this->placeholders_options[$name]);
			}
			if(!isset($defaults['options']['default_render'])){
				$defaults['options']['default_render'] = $defaults['default_render'];
			}
			$placeholder = new Template\Placeholder($this, $defaults['type'], $defaults['arguments'], $defaults['options']);
			$placeholder->setName($name);
			if($defaults['setArguments']){
				$placeholder->replaceTypeArguments($defaults['setArguments']);
			}
			$placeholder->setCustom($defaults['pattern'], $defaults['evaluator'], $defaults['renderer']);
			$placeholder->setAfter($defaults['after']);
			$placeholder->setBefore($defaults['before']);
			$placeholder->setOptional((bool)$defaults['optional'],$defaults['default']);
			return $placeholder;
		}


		/**
		 * @patten {/?}user/{user.id:list(/)}
		 */
		protected function _compile(){
			$this->placeholder_names = null;
			$this->compiled = true;
			$phRegex = $this->getPlaceholderRegex();
			$modifiers = $this->getOption('modifiers');
			$i = 0;
			$regex = preg_replace_callback($phRegex,function($m) use(&$i, $modifiers){
				if(isset($m[10]) && $m[10]){
					$this->composite[] = $m[10];
					if($modifiers){
						return '(?'.$modifiers.':'.preg_quote($m[10],'@').')';
					}else{
						return preg_quote($m[10],'@');
					}

				}else{
					$param_name = $m[1];
					$optional   = isset($m[2]) && $m[2]?$m[2]:false;
					$before     = isset($m[3]) && $m[3]?$m[3]:null;
					$after      = isset($m[4]) && $m[4]?$m[4]:null;
					$type       = isset($m[5]) && $m[5]?$m[5]:'string';
					$type_args  = isset($m[6]) && $m[6]?array_map('trim',explode(isset($m[7]) && $m[7]?$m[7]:$this->getOption('args_separator',','), $m[6])):[];
					$pattern    = isset($m[8]) && $m[8]?$m[8]:null;
					$default    = isset($m[9]) && $m[9]?$m[9]:null;
					$this->composite[$param_name] = $m[0];
					$placeholder = [
						'type'      => $type,
						'arguments' => $type_args,
						'pattern'   => $pattern,
						'after'     => $after,
						'before'    => $before,
						'optional'  => $optional,
					];
					if($default){
						$placeholder['default'] = $default;
					}
					$placeholder = $this->makePlaceholder($param_name,$placeholder);
					$this->placeholders[$param_name] = $placeholder;
					$i++;
					return $placeholder->compile();
				}
			},$this->definition);
			$modifiers = $this->getOption('global_modifiers');
			if($this->getOption('soft')){
				$this->regex = '@'.addcslashes($regex,'@').'@Sms'.$modifiers;
			}else{
				$this->regex = '@\A'.addcslashes($regex,'@').'\Z@Sms'.$modifiers;
			}
		}

		/**
		 * @param $key
		 * @param null $default
		 * @return null
		 */
		public function getOption($key, $default = null){
			if(!isset($this->options[$key])){
				return $this->manager->getTemplateOption($key,$default);
			}else{
				return $this->options[$key];
			}
		}

		/**
		 * @param array $data
		 * @return string
		 * @throws Exception
		 * @throws Placeholder\Exception
		 */
		public function render($data){
			if(!$this->compiled){
				$this->_compile();
			}
			$subject = $this->composite;
			foreach($this->placeholders as $name => $placeholder){
				if(!isset($subject[$name])){
					throw new Exception('Internal error: composite data error');
				}
				$subject[$name] = $placeholder->render(isset($data[$name])?$data[$name]:null);
			}
			return implode('',$subject);
		}

		/**
		 * @param $subject
		 * @return array|bool
		 */
		public function match($subject){
			if(!is_string($subject)){
				trigger_error('arguments subject must be string, a "'.gettype($subject).'" given',E_USER_WARNING);
			}
			if(!$this->compiled){
				$this->_compile();
			}
			$data = [];
			if(preg_match($this->regex, $subject, $matches) > 0){
				foreach($this->placeholders as $name => $ph){
					$n = $ph->getNameRegex();
					$data[$name] = $ph->evaluate(isset($matches[$n])?$matches[$n]:null);
				}
				return $data;
			}
			return false;
		}

		/**
		 * @param Manager $manager
		 * @return $this
		 */
		public function setManager(Manager $manager){
			$this->manager = $manager;
			return $this;
		}

		/**
		 * @return Manager
		 */
		public function getManager(){
			return $this->manager;
		}

		/**
		 * @return string
		 */
		public function getDefinition(){
			return $this->definition;
		}

		/**
		 * @return array
		 */
		public function getOptions(){
			return $this->options;
		}

		/**
		 * @return array
		 */
		public function getPlaceholderOptions(){
			return $this->placeholders_options;
		}


	}
}

