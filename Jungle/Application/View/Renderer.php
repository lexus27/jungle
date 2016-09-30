<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.07.2016
 * Time: 21:09
 */
namespace Jungle\Application\View {

	use Jungle\Application\Component;
	use Jungle\Application\Dispatcher\ProcessInterface;
	use Jungle\Application\ViewInterface;
	use Jungle\FileSystem;

	/**
	 * Class Renderer
	 * @package Jungle\Application\View
	 */
	abstract class Renderer extends Component implements RendererInterface{

		/** @var  string */
		protected $name;

		/** @var  ViewInterface */
		protected $view;

		/** @var  string */
		protected $type;

		/** @var  string */
		protected $mime_type;

		/** @var  array */
		protected $variables = [];

		/** @var  array */
		protected $options = [];

		/** @var  bool */
		protected $initialized = false;

		/** @var  ProcessInterface */
		protected $process;

		/**
		 * @param ViewInterface $view
		 * @return $this
		 */
		public function setView(ViewInterface $view){
			if($this->view !== $view){
				$this->view = $view;
				$this->name = null;
			}
			return $this;
		}

		/**
		 * @return ViewInterface
		 */
		public function getView(){
			return $this->view;
		}


		/**
		 * Twig constructor.
		 * @param null $mime_type
		 * @param array $options
		 * @param array $variables
		 */
		public function __construct($mime_type = null, array $options = [], array $variables = []){
			if($mime_type!==null){
				$this->mime_type = $mime_type;
			}
			$this->variables = $variables;
			$this->options = $options;
		}

		/**
		 * @return string
		 */
		public function getName(){
			if(is_null($this->name)){
				$this->name = $this->view->getRendererName($this);
			}
			return $this->view->getRendererName($this);
		}


		/**
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function setVar($key, $value){
			$this->variables[$key] = $value;
			return $this;
		}

		/**
		 * @param $key
		 * @return mixed|null
		 */
		public function getVar($key){
			return isset($this->variables[$key])?$this->variables[$key]:null;
		}

		/**
		 * @param array $variables
		 * @param bool|false $merge
		 * @return $this
		 */
		public function setVars(array $variables = [ ], $merge = false){
			$this->variables = $merge?array_replace($this->variables,$variables):$variables;
			return $this;
		}


		/**
		 * @param $name
		 * @param $value
		 * @return mixed
		 */
		public function setOption($name, $value){
			$this->options[$name] = $value;
			return $this;
		}

		/**
		 * @param $name
		 * @param null $default
		 * @return mixed
		 */
		public function getOption($name, $default = null){
			return isset($this->options[$name])?$this->options[$name]:$default;
		}

		/**
		 * @param array $options
		 * @param bool|false|false $merge
		 * @return $this
		 */
		public function setOptions(array $options, $merge = false){
			$this->options = $merge?array_replace($this->options,$options):$options;
			return $this;
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
		public function getVariables(){
			return $this->variables;
		}

		/**
		 * @return string
		 */
		public function getType(){
			return $this->type;
		}

		/**
		 * @param $type
		 * @return $this
		 */
		public function setMimeType($type){
			$this->mime_type = $type;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getMimeType(){
			return $this->mime_type;
		}

		/**
		 * Renderer Initializing.
		 */
		public function initialize(){
			if(!$this->initialized){
				$this->initialized = true;
				$this->_doInitialize();
			}
		}

		/**
		 * @return void
		 */
		abstract protected function _doInitialize();

		/**
		 * @return string
		 */
		public function getBaseDirname(){
			return $this->view->getBaseDirname() . DIRECTORY_SEPARATOR . $this->getName();
		}

		/**
		 * @return string
		 */
		public function getCacheDirname(){
			return $this->view->getCacheDirname() . DIRECTORY_SEPARATOR . $this->getName();
		}

		/**
		 * @return bool
		 */
		public function cacheIsEnabled(){
			return $this->options['cacheable'];
		}
		/**
		 * Cache this object clear.
		 * @return $this
		 */
		public function cacheClear(){
			$dirname = $this->getCacheDirname();
			FileSystem::removeContain($dirname);
			return $this;
		}

		/**
		 * Enables cache in this object context.
		 * @return $this
		 */
		public function cacheOn(){
			$this->options['cacheable'] = true;
			return $this;
		}

		/**
		 * Disables cache in this object context.
		 * @return $this
		 */
		public function cacheOff(){
			$this->options['cacheable'] = false;
			return $this;
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function getService($name){
			if(static::$_dependency_injection_cacheable){
				if(!array_key_exists($name,$this->_dependency_injection_cache)){
					$result = $this->_dependency_injection->get($name);
					$this->_dependency_injection_cache[$name] = $result;
					return $result;
				}
				return $this->_dependency_injection_cache[$name];
			}else{
				if(!$this->_dependency_injection){
					throw new \LogicException('DependencyInjector is not supplied in object');
				}
				return $this->getDi()->get($name);
			}
		}



	}
}

