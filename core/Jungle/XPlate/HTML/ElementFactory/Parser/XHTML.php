<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 28.04.2015
 * Time: 23:24
 */

namespace Jungle\XPlate\HTML\ElementFactory\Parser {


	use Jungle\XPlate\HTML\Document;
	use Jungle\XPlate\HTML\ElementFactory\Parser;

	/**
	 * Class HTML
	 * @package Jungle\XPlate2\HTML\ElementFactory\Parser
	 *
	 * Very Nice regex
	 * @
		(<(\w+)(?:([^>]+))?>(.*)<\/((?i)\2)>) | #Tag Structure
		(<!--(.*)-->) |                         #Comment
		(<!([^>]+)>) |                          #Doctype
		([^<>]+)                                #Simple Text
	 * @msx
	 */
	class XHTML extends Parser{

		/**
		 * @var string
		 */
		protected $regex = '@
	(                                        # First mask
	<!?
		([\w]+)                              #Tag name [2/]
		([^>]*?)                             #Attributes raw string [3/]
		(                                    #Body [4]
			([\s]*\/>)                       #Ommit [5]
				|
			(                                #This element is container [6]
				>
				(                            #INNER wrap [7]
					(                        #INNER html or text collection [8]
						(                    #Inner text [9]
							[^<]*?
								|
							<\!\-\-.*?\-\->
						)
							|
						(?R)                 #Inner html [10/]
					)*
				)
				<\/\\2[^>]*>
			)
		)
		|((?!<\<)([^<>]+)(?!\>)|<[^>]+>)     #Whitespaces between tags [11/]
	)
@xsm';

		/**
		 *
		 */
		public function __construct(){
			parent::__construct('xhtml');
		}

		/**
		 * @param $string_definition
		 * @return null
		 */
		public function match($string_definition){
			$result = null;
			preg_match_all($this->getRegex(), $string_definition, $result, PREG_OFFSET_CAPTURE);
			return $result ? $result : false;
		}

		/**
		 * @param $string_definition
		 * @param null $result
		 * @param bool|false $preserveFormat
		 * @param int $depth
		 * @return array
		 */
		protected function processParse(& $string_definition, $result = null, $preserveFormat = false, $depth = 0){
			$factory = $this->getFactory();
			$row = [];
			if($result === null){
				$result = $this->match($string_definition);
			}
			unset($string_definition);
			if($result){
				foreach($result[0] as $i => & $match){
					if($match[0]){
						$tag = (strtolower(is_array($result[2][$i]) ? $result[2][$i][0] : null));
						if($tag){
							$el = $this->findElementForTag(strtolower($tag));
							if(!$el){
								$el = $factory->instantiateElement(false);
							}
							$el->setTag($tag);
							$el->setClosed((isset($result[5][$i][1]) && $result[5][$i][1] > -1));

							$attributes = $this->parseAttributes(isset($result[3][$i][0]) ? $result[3][$i][0] : '');
							foreach($attributes as $aK => $aV){
								$el->setAttribute($aK, $aV);
							}

							if(!$el->isClosed() && (isset($result[7][$i][0]) && $result[7][$i][0])){
								$model = $this->processParse(
									$result[7][$i][0], null,
									!$preserveFormat ? Document::isChildrenFormatPreserved($tag) : $preserveFormat,
									$depth + 1
								);
								foreach($model as $ch){
									$el->append($ch);
								}
							}
							$row[] = $el;
						}else{
							if(!$preserveFormat && $this->isWhitespace($match[0])){
								$factory->pcOnWhitespace();
							}else{
								$el = $factory->instantiateElement(true);
								$el->setValue($match[0]);
								$factory->pcOnNormal($el);
								$row[] = $el;
							}
						}
					}
				}
			}else{
				$factory->throwParseError($this->getAlias());
			}
			return $row;
		}

		protected function findElementForTag($tagName){
			return null;
		}

	}

}