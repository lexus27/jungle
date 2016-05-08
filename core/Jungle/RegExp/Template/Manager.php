<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.04.2016
 * Time: 22:29
 */
namespace Jungle\RegExp\Template {

	use Jungle\RegExp\Template;
	use Jungle\RegExp\Type;
	use Jungle\RegExp\Type\TypeAwareInterface;

	/**
	 * Class Manager
	 * @package Jungle\RegExp\Template
	 */
	class Manager implements TypeAwareInterface{


		/** @var Manager */
		protected static $_default_manager;

		/** @var  Manager */
		protected $type_registry;

		/** @var  array  */
		protected $placeholder_defaults = [];

		/** @var  array  */
		protected $template_defaults;

		/**
		 * @return Manager
		 */
		public static function getDefault(){
			if(!self::$_default_manager){
				self::$_default_manager = new Manager();
			}
			return self::$_default_manager;
		}

		/**
		 * @param array $defaults
		 * @return $this
		 */
		public function setTemplateDefaults(array $defaults){
			$this->template_defaults = $defaults;
			return $this;
		}

		/**
		 * @return array
		 */
		public function getTemplateDefaults(){
			return $this->template_defaults;
		}

		/**
		 * @param $key
		 * @param null $default
		 * @return null
		 */
		public function getTemplateOption($key, $default = null){
			return isset($this->template_defaults[$key])?$this->template_defaults[$key]:$default;
		}

		/**
		 * @param $placeholder_name
		 * @return null
		 */
		public function getPlaceholderDefaults($placeholder_name){
			if(isset($this->placeholder_defaults[$placeholder_name])){
				return $this->placeholder_defaults[$placeholder_name];
			}else{
				return null;
			}
		}

		/**
		 * @param $placeholder_name
		 * @param $options
		 * @return $this
		 */
		public function setPlaceholderDefaults($placeholder_name, array $options){
			$this->placeholder_defaults[$placeholder_name] = $options;
			return $this;
		}

		/**
		 * @param Manager $default
		 */
		public static function setDefault(Manager $default){
			self::$_default_manager = $default;
		}

		/**
		 * @param $name
		 * @return Type|null
		 */
		public function getType($name){
			if(!$this->type_registry){
				$this->type_registry = Type\Manager::getDefault();
			}
			return $this->type_registry->getType($name);
		}

		/**
		 * @param $pattern
		 * @param array $placeholders_options
		 * @param array $options
		 * @return Template
		 */
		public function template($pattern, array $placeholders_options = null, array $options = null){
			$template = new Template($pattern,$this, $placeholders_options, $options);
			$template->setManager($this);
			return $template;
		}

		/**
		 * @return mixed
		 */
		public static function getPlaceholderRegex(){
			return '@\{((?:[\-#=\@!%$*+?])?\w[\w\-\.\d]*)(\?)?(?:<(.*?)?\|(.*?)?>)?(?::([\w&][\w\.\-\d\s]*(?:\[\])?)(?:\(([^{]+?)\)(?:\(([^\}])\))?)?)?(?::\(([^{]+)\))?(?:=([^}]*))?\}|(\{?[^{]+)@S';
		}


	}
}

