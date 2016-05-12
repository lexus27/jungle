<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 11.05.2016
 * Time: 22:57
 */


namespace TemplateIn;


/**
 * Class TemplateManager
 * @package TemplateIn
 */
class TemplateManager{

	/** @var  ScopeInterface */
	protected $scope;

	/**
	 * @param ScopeInterface $scope
	 * @return $this
	 */
	public function setScope(ScopeInterface $scope){
		$this->scope = $scope;
		return $this;
	}

	/**
	 * @return Scope|ScopeInterface
	 */
	public function getScope(){
		if(!$this->scope){
			$this->scope = new Scope();
		}
		return $this->scope;
	}

	/**
	 * @param Template $template
	 * @param ScopeDecorator $scope
	 * @return string
	 */
	public function renderTemplate(Template $template,ScopeDecorator $scope){
		$scope->setParent($this->scope);
		return $template->render($scope);
	}

	/**
	 * @param $key
	 * @param $value
	 * @return $this
	 */
	public function registerVariable($key, $value){
		$this->getScope()->registerVariable($key, $value);
		return $this;
	}

	/**
	 * @param $key
	 * @return $this
	 */
	public function unregisterVariable($key){
		$this->getScope()->unregisterVariable($key);
		return $this;
	}

	/**
	 * @param $functionKey
	 * @param callable $function
	 * @return $this
	 */
	public function registerFunction($functionKey, callable $function = null){
		$this->getScope()->registerFunction($functionKey,$function?$function:$functionKey);
		return $this;
	}

	/**
	 * @param $functionKey
	 * @return $this
	 */
	public function unregisterFunction($functionKey){
		$this->getScope()->unregisterFunction($functionKey);
		return $this;
	}

}

/**
 * Class Template
 */
class Template{

	/** @var  Template|null */
	protected $ancestor;

	/** @var  SectionInterface[]  */
	protected $sections = [];

	/** @var  SectionInterface[]  */
	protected $named_sections = [];

	/**
	 * @param Template $ancestor
	 * @return $this
	 */
	public function setAncestor(Template $ancestor){
		$this->ancestor = $ancestor;
		return $this;
	}

	/**
	 * @return Template|null
	 */
	public function getAncestor(){
		return $this->ancestor;
	}

	/**
	 * @return SectionInterface[]
	 */
	public function getSections(){
		if($this->ancestor){
			$sections = $this->ancestor->getSections();
		}else{
			return $this->sections;
		}
		foreach($sections as $key => & $section){
			$ownSection = $this->getSection($key);
			if($ownSection){
				$section = $ownSection;
			}

		}
		return $sections;
	}

	/**
	 * @param $key
	 * @return null|SectionInterface
	 */
	public function getSection($key){
		return isset($this->sections[$key])? $this->sections[$key]:null;
	}


	/**
	 * @param ScopeInterface $scope
	 * @return string
	 */
	public function render(ScopeInterface $scope){
		$stack = '';
		$sections = $this->getSections();
		foreach($sections as $key => $section){
			$stack.=$section->render($scope);
		}
		return $stack;
	}

	/**
	 * @param SectionInterface $section
	 * @param null $key
	 * @return $this
	 */
	public function push(SectionInterface $section, $key = null){
		if($key === null){
			$this->sections[] = $section;
		}else{
			$this->sections[$key] = $section;
			$this->named_sections[$key] = $section;
		}
		return $this;
	}

}
interface ScopeInterface{

	/**
	 * @param $key
	 * @return mixed
	 */
	public function getVariable($key);

	/**
	 * @param $key
	 * @return bool
	 */
	public function hasVariable($key);

	/**
	 * @param $key
	 * @param $value
	 * @return $this
	 */
	public function registerVariable($key, $value);

	/**
	 * @param $key
	 * @return $this
	 */
	public function unregisterVariable($key);

	/**
	 * @param $key
	 * @param array $arguments
	 * @return mixed
	 */
	public function callFunction($key, array $arguments = []);

	/**
	 * @param $key
	 * @param callable $function
	 * @return $this
	 */
	public function registerFunction($key, callable $function);

	/**
	 * @param $key
	 * @return $this
	 */
	public function unregisterFunction($key);

	/**
	 * @param $key
	 * @return callable|null
	 */
	public function getFunction($key);
}

interface SectionInterface{

	public function render(ScopeInterface $scope);

}



/**
 * Class Section
 * @package Template
 */
abstract class Section implements SectionInterface{

	/** @var  Section|null */
	protected $container;

	/**
	 * @param Section $container
	 * @return $this
	 */
	public function setContainer(Section $container){
		$this->container = $container;
		return $this;
	}

	/**
	 * @return Section|null
	 */
	public function getContainer(){
		return $this->container;
	}
}

class BlockSection extends Section{

	/** @var  SectionInterface[] */
	protected $children = [];

	/** @var array  */
	protected $named_children = [];

	/**
	 * @param ScopeInterface $scope
	 * @return string
	 */
	public function render(ScopeInterface $scope){
		$stack = '';
		foreach($this->children as $key => $section){
			$stack.=$section->render($scope);
		}
		return $stack;
	}


	/**
	 * @param SectionInterface $section
	 * @param null $key
	 * @return $this
	 */
	public function addChild(SectionInterface $section, $key = null){
		if($key === null){
			$this->children[] = $section;
		}else{
			$this->children[$key] = $section;
			$this->named_children[$key] = $section;
		}
		return $this;
	}

}

/**
 * Class TextSection
 * @package Template
 */
class TextSection extends Section implements ElementHolderInterface{

	/** @var  ElementInterface[] */
	protected $elements = [];

	public function render(ScopeInterface $scope){
		$stack = '';
		foreach($this->elements as $element){
			$stack.=$element->render($scope);
		}
		return $stack;
	}

	/**
	 * @param ElementInterface $element
	 * @return $this
	 */
	public function addElement(ElementInterface $element){
		$this->elements[] = $element;
		return $this;
	}

}

interface ElementHolderInterface{}
interface ElementInterface{

	public function render(ScopeInterface $scope);

}

abstract class Element implements ElementInterface{



}

/**
 * Class SimpleElement
 * @package Template
 */
class SimpleElement extends Element{

	protected $text = '';

	/**
	 * SimpleElement constructor.
	 * @param $text
	 */
	public function __construct($text){
		$this->text = $text;
	}

	/**
	 * @param ScopeInterface $scope
	 * @return string
	 */
	public function render(ScopeInterface $scope){
		return $this->text;
	}
}
class VariableElement extends Element{

	/** @var  string */
	protected $varName;

	/** @var mixed @TODO "modifiers" */
	protected $defaultValue;

	/** @var bool  */
	protected $required = false;

	/**
	 * VariableElement constructor.
	 * @param $varName
	 * @param bool|true $required
	 * @param null $default
	 */
	public function __construct($varName, $required = true, $default = null){
		$this->varName = $varName;
	}

	/**
	 * @param ScopeInterface $scope
	 * @return mixed
	 */
	public function render(ScopeInterface $scope){

		$exists = $scope->hasVariable($this->varName);
		if($this->required && !$exists){
			throw new \LogicException('Required variable "'.$this->varName.'"!');
		}elseif(!$this->required && !$exists){
			return $this->defaultValue;
		}

		$value = $scope->getVariable($this->varName);

		//Modifiers handle!

		return $value;

	}
}

/**
 * Class FunctionCallElement
 * @package TemplateIn
 */
class FunctionCallElement extends Element{

	/** @var  string */
	protected $function_name;

	/** @var array  */
	protected $arguments = [];

	/**
	 * FunctionCallElement constructor.
	 * @param $functionName
	 * @param array $arguments
	 */
	public function __construct($functionName, array $arguments = []){
		$this->function_name = $functionName;
		$this->arguments = $arguments;
	}

	/**
	 * @param ScopeInterface $scope
	 * @return mixed
	 */
	public function render(ScopeInterface $scope){
		return $scope->callFunction($this->function_name, $this->arguments);
	}

}
class VariableDefineElement extends Element{

	/** @var  string */
	protected $key;

	/** @var  mixed */
	protected $value;

	public function __construct($key, $value){

		$this->key = $key;
		$this->value = $value;
	}

	public function render(ScopeInterface $scope){
		$scope->registerVariable($this->key, $this->value);
		return '';
	}

}

/**
 * Class LogicalBranchElement
 * @package TemplateIn
 */
class LogicalBranchElement extends ComplexElement{

	const COND_IF       = 0;
	const COND_ELSE_IF  = 1;
	const COND_ELSE     = 2;

	protected $current_cond_type = 0;

	protected $current_else_if_cond = -1;

	/** @var  mixed */
	protected $if_condition;

	/** @var  SectionInterface[]  */
	protected $if_condition_sections = [];


	/** @var  mixed[] */
	protected $else_if_conditions = [];

	/** @var  array[]|SectionInterface[]  */
	protected $else_if_conditions_sections = [];

	/** @var  SectionInterface[]  */
	protected $else_sections = [];

	/**
	 * @param $condition
	 * @param ScopeInterface $scope
	 * @return bool
	 */
	protected function _matchCondition($condition, ScopeInterface $scope){
		return true;
	}

	/**
	 * @param SectionInterface[] $sections
	 * @param ScopeInterface $scope
	 * @return string
	 */
	public function _renderSections(array $sections,ScopeInterface $scope){
		$s = '';
		foreach($sections as $section){
			$s.=$section->render($scope);
		}
		return $s;
	}
	
	
	/**
	 * @param ScopeInterface $scope
	 * @return string
	 */
	public function render(ScopeInterface $scope){
		if($this->_matchCondition($this->if_condition,$scope)){
			return $this->_renderSections((array)$this->if_condition_sections,$scope);
		}
		if($this->else_if_conditions){
			foreach($this->else_if_conditions as $i => $condition){
				if($this->_matchCondition($condition, $scope)){
					return $this->_renderSections((array)$this->else_if_conditions_sections[$i],$scope);
				}
			}
		}
		if($this->else_sections){
			return $this->_renderSections((array)$this->else_sections,$scope);
		}
		return '';
	}

	/**
	 * @param $condition
	 * @return $this
	 */
	public function ifCondition($condition){
		$this->current_cond_type = self::COND_IF;
		$this->if_condition = $condition;
		return $this;
	}

	/**
	 * @param $condition
	 * @return $this
	 */
	public function elseIfCondition($condition){
		$this->current_cond_type = self::COND_ELSE_IF;
		$this->current_else_if_cond++;
		$this->else_if_conditions[$this->current_else_if_cond] = $condition;
		$this->else_if_conditions_sections[$this->current_else_if_cond] = [];
		return $this;
	}

	/**
	 * @return $this
	 */
	public function elseCondition(){
		$this->current_cond_type = self::COND_ELSE;
		return $this;
	}

	/**
	 * @param SectionInterface $section
	 * @param null $key
	 * @return $this
	 */
	public function push(SectionInterface $section, $key = null){
		$condType = $this->current_cond_type;
		switch($condType){
			case self::COND_IF:
				$count = count($this->if_condition_sections);
				$this->if_condition_sections[$key===null?$count:$key] = $section;
				break;
			case self::COND_ELSE_IF:
				$current = & $this->else_if_conditions_sections[$this->current_else_if_cond];
				$count = count($current);
				$current[$key===null?$count:$key] = $section;
				break;
			case self::COND_ELSE:
				$count = count($this->else_sections);
				$this->else_sections[$key===null?$count:$key] = $section;
				break;
		}
		return $this;
	}

}

/**
 * Class ComplexElement
 * @package TemplateIn
 */
abstract class ComplexElement extends Element{

	/** @var  ScopeInterface|null */
	protected $scope;

	/** @var  SectionInterface[]  */
	protected $sections = [];

	/**
	 * @return null|ScopeDecorator|ScopeInterface
	 */
	public function getScope(){
		if(!$this->scope){
			$this->scope = new ScopeDecorator();
		}
		return $this->scope;
	}


	public function push(SectionInterface $section){
		$this->sections[] = $section;
		return $this;
	}


}

/**
 * Class LoopElement
 * @package TemplateIn
 */
class LoopElement extends ComplexElement implements ElementHolderInterface{

	/** @var  string */
	protected $from;

	/** @var  string */
	protected $key;

	/** @var  string */
	protected $val;

	/** @var  SectionInterface[]  */
	protected $empty = [];

	public function __construct($from, $key, $val){
		$this->from = $from;
		$this->key = $key;
		$this->val = $val;
	}

	/**
	 * @param ScopeInterface $scope
	 * @return string
	 */
	public function render(ScopeInterface $scope){
		$stack = '';
		$collection = $scope->getVariable($this->from);
		if($collection){
			$local = $this->getScope();
			$local->clear();
			$local->setParent($scope);
			foreach($collection as $key => $value){
				if($this->key){
					$local->registerVariable($this->key,$key);
				}
				$local->registerVariable($this->val,$value);
				foreach($this->sections as $section){
					$stack .= $section->render($local);
				}
			}
		}else{
			foreach($this->empty as $key => $section){
				$stack.=$section->render($scope);
			}
		}
		return $stack;
	}

	public function emptyPush(SectionInterface $section){
		$this->empty[] = $section;
		return $this;
	}


}

/**
 * Class Scope
 * @package Template
 */
class Scope implements ScopeInterface{

	/** @var  array  */
	protected $variables = [];

	/** @var  array  */
	protected $functions = [];

	/**
	 * @param $key
	 * @return mixed
	 */
	public function getVariable($key){
		return isset($this->variables[$key])?$this->variables[$key]:null;
	}

	/**
	 * @param $key
	 * @return bool
	 */
	public function hasVariable($key){
		return isset($this->variables[$key]);
	}

	/**
	 * @param $key
	 * @param $value
	 * @return $this
	 */
	public function registerVariable($key, $value){
		$this->variables[$key] = $value;
		return $this;
	}

	/**
	 * @param array $variables
	 * @param bool|true $merge
	 */
	public function setVariables(array $variables, $merge = true){
		$this->variables = $merge?array_replace($this->variables,$variables):$variables;
	}

	/**
	 *
	 */
	public function clear(){
		$this->variables = [];
	}

	/**
	 * @param $key
	 * @param array $arguments
	 * @return mixed|null
	 */
	public function callFunction($key, array $arguments = [ ]){
		if(isset($this->functions[$key])){
			return call_user_func_array($this->functions[$key],$arguments);
		}else{
			return null;
		}
	}

	/**
	 * @param $key
	 * @param callable $function
	 * @return $this
	 */
	public function registerFunction($key, callable $function){
		$this->functions[$key] = $function;
		return $this;
	}

	/**
	 * @param $key
	 * @return $this
	 */
	public function unregisterVariable($key){
		unset($this->variables[$key]);
		return $this;
	}

	/**
	 * @param $key
	 * @return $this
	 */
	public function unregisterFunction($key){
		unset($this->functions[$key]);
		return $this;
	}

	/**
	 * @param $key
	 * @return callable|null
	 */
	public function getFunction($key){
		return isset($this->functions[$key])?$this->functions[$key]:null;
	}
}

/**
 * Class ScopeDecorator
 * @package Template
 */
class ScopeDecorator extends Scope{

	/** @var  ScopeInterface */
	protected $parent;

	/**
	 * ScopeDecorator constructor.
	 * @param ScopeInterface $parent
	 */
	public function __construct(ScopeInterface $parent = null){
		$this->parent = $parent;
	}

	/**
	 * @param ScopeInterface $parent
	 * @return $this
	 */
	public function setParent(ScopeInterface $parent){
		$this->parent = $parent;
		return $this;
	}

	/**
	 * @param $key
	 * @return null
	 */
	public function getVariable($key){
		if(isset($this->variables[$key])){
			return $this->variables[$key];
		}elseif($this->parent){
			return $this->parent->getVariable($key);
		}else{
			return null;
		}
	}

	/**
	 * @param $key
	 * @return bool
	 */
	public function hasVariable($key){
		if(isset($this->variables[$key])){
			return true;
		}elseif($this->parent){
			return $this->parent->hasVariable($key);
		}else{
			return false;
		}
	}

	/**
	 * @param $key
	 * @param array $arguments
	 * @return mixed|null
	 */
	public function callFunction($key, array $arguments = [ ]){
		if(isset($this->functions[$key])){
			return call_user_func_array($this->functions[$key],$arguments);
		}elseif($this->parent){
			return $this->parent->callFunction($key,$arguments);
		}else{
			return null;
		}
	}

	/**
	 * @param $key
	 * @return callable|null
	 */
	public function getFunction($key){
		if(isset($this->functions[$key])){
			return $this->functions[$key];
		}elseif($this->parent){
			return $this->parent->getFunction($key);
		}else{
			return null;
		}
	}

}
$mgr = new TemplateManager();
$mgr->registerFunction('strtolower');
$mgr->registerFunction('strtoupper');
$mgr->registerFunction('ucfirst');
$mgr->registerVariable('_SESSION',$_SESSION);

$template = new Template();
$template->push(
	(new TextSection())
		->addElement( new SimpleElement('<h1>Heading element</h1>') )
);
$template->push(
	(new TextSection())
		->addElement( new SimpleElement('<h2>Пользователи сказавшие спасибо</h2><ul>') )
		->addElement(
			(new LoopElement('user_names','collectionKey','collectionVal'))
			->push(
				(new TextSection())
					->addElement((new SimpleElement( '<li>User (' )))
					->addElement((new VariableElement('collectionVal',false,'Имя не указано')))
					->addElement((new SimpleElement( ')</li>' )))
			)
			->emptyPush(
				(new TextSection())
					->addElement((new SimpleElement('<li><b>Пока никто не сказал спасибо</b></li>')))
			)
		)
		->addElement( new SimpleElement('</ul>') )
	,'thanks'
);
$template->push(
	(new TextSection())
		->addElement( new SimpleElement('<h2>Пользователи проголосовавшие</h2><ul>') )
		->addElement(
			(new LoopElement('user_votes','collectionKey','collectionVal'))
				->push(
					(new TextSection())
						->addElement((new SimpleElement( '<li>' )))
						->addElement((new VariableElement('collectionKey')))
						->addElement((new SimpleElement( ' оценил на ' )))
						->addElement((new VariableElement('collectionVal')))
						->addElement((new SimpleElement( ' балл(а/ов) ' )))
						->addElement((new SimpleElement( '</li>' )))
				)
				->emptyPush(
					(new TextSection())
						->addElement((new SimpleElement('<li><b>Никто не голосовал</b></li>')))
				)
		)
		->addElement( new SimpleElement('</ul>') )
	,'votes'
);
$scope = new Scope();
$scope->registerVariable('user_names',[
	'Василий',
    'Анатолий',
    'Евгения',
    'Игорь'
]);
$scope->registerVariable('user_votes',[
	'Василий' => 4,
	'Анатолий' => 4,
	'Евгения' => 4,
	'Игорь' => 4
]);
echo '<div style="border:solid 1px;padding:10px"><h1>Родительский шаблон</h1>';
echo $template->render($scope);
echo '</div>';


$template2 = new Template();
$template2->setAncestor($template);

$template->push(
	(new TextSection())
		->addElement( new SimpleElement('<h2>Пользователи сказавшие спасибо</h2><ul>') )
		->addElement(
			(new LoopElement('user_names','collectionKey','collectionVal'))
				->push(
					(new TextSection())
						->addElement((new SimpleElement( '<li> <span style="color:red;">Пользователь: </span><span style="color: darkslateblue;"> ' )))
						->addElement((new VariableElement('collectionVal',false,'Имя не указано')))
						->addElement((new SimpleElement( '</span></li>' )))
				)
				->emptyPush(
					(new TextSection())
						->addElement((new SimpleElement('<li><b>Пока никто не сказал спасибо</b></li>')))
				)
		)
		->addElement( new SimpleElement('</ul>') )
	,'thanks'
);
echo '<br/>';
echo '<br/>';
echo '<br/>';
echo '<br/>';
echo '<div style="border:solid 1px;padding:10px"><h1>Переопределенный шаблон от родитеского</h1>';
echo $template2->render($scope);
echo '</div>';


/**
 *
 * Есть неопределенности и задачи на будующее:
 *          1 Локальные SCOPE для блоков
 *          2 Блоки IF/ELSE и другие модифицирующие/логические блоки
 *          3 Переопределение вложеных блоков Block Nesting! в потомках
 *
 */