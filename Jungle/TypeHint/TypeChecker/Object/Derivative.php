<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 28.10.2015
 * Time: 0:41
 */
namespace Jungle\TypeHint\TypeChecker\Object {

	use Jungle\TypeHint;
	use Jungle\TypeHint\TypeChecker\Object;
	use Jungle\Util\Value\String;

	/**
     * Class ObjectDerivative
     * @package Jungle\TypeHint\TypeChecker\Object
     */
    class Derivative extends Object{

        /** @var string  */
        protected $name = 'derivative';

        /** @var array  */
        protected $alias = [];

        /** @var int  */
        protected $priority = -100;

        /** @var string  */
        protected $error_message = 'Passed "{{valueGetType}}":"{{value}}" value {{param}} is not derived of {{type}}!';

        /**
         *
         */
        public function getVerified(){
            return $this->verified;
        }

        /**
         * @param mixed $value
         * @param array $parameters
         * @return bool
         */
		public function check($value, array $parameters = []){
            list($verified, $instanceStrict, $useTraits) = $this->getVerified();
            if($instanceStrict && !is_object($value)){
                return false;
            }
            if($useTraits){
                return in_array($verified,(new \ReflectionClass($value))->getTraitNames(),true);
            }else{
                return is_a($value,$verified,true);
            }
		}

        /**
         * @param string $type
         *@return bool
         */
        public function verify($type){
            $instanceStrict     = true;
            $useTraits          = false;

            if(String::startWith('instanceof ',$type,true)){
                $type = String::trimWordsLeft($type,'instanceof ');
            }elseif(String::startWith('derivedof ',$type,true)){
                $instanceStrict = false;
                $type = String::trimWordsLeft($type,'derivedof ');
            }

            $type = ltrim($type,"\t\n\r\0\x0B\\");
            if(!class_exists($type)){
                if(trait_exists($type)){
                    $useTraits = true;
                }else{
                    $this->last_internal_error_message = 'Class or Trait "'.$type.'" is not exists!';
                    return false;
                }
            }
            $this->verified = [$type,$instanceStrict,$useTraits];
            return true;
        }

	}
}

