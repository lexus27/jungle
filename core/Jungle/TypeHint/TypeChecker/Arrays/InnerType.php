<?php
/**
 * Created by PhpStorm.
 * Project: jungle
 * Author: HoMe
 * Date: 14.11.2015
 * Time: 5:02
 */
namespace Jungle\TypeHint\TypeChecker\Arrays{

    use Jungle\TypeHint;
    use Jungle\TypeHint\TypeChecker\Arrays;

    /**
     * Class Derivative
     * @package Jungle\TypeHint\TypeChecker\Arrays
     * Array[] -
     * array(1,2,3,4,5)
     *
     * {{object[]}[]}[] -
     * Значение массив, состоящий из массивов, который тоже состоит из массивов в которых содержатся объекты
     *
     * array(
     *      array(
     *          array(Object,Object,Object,Object)
     *      ),
     *      array(
     *          array(Object,Object,Object,Object)
     *      )
     * )
     *
     * {{object[]|numeric}[]}[] -
     * немного модифицированное определение,
     * заместо массива объектов элементом может быть еще и число
     *
     * array()
     *
     *
     *
     */
	class InnerType extends Arrays{

        /**
         * @param mixed $value
         * @param array $parameters
         * @return bool TODO TASK
         *
         * TODO TASK
         * Раскидать методы, рассосредоточив их по субъектам хинтинга, для более детальной архитектуры компонента
         * Так-же требуется сделать Хинтинг с поддержкой параметров к Типам, типо
         *
         * array(Associative,MinCount:1,MaxCount:4)
         *
         * string(MinLen:1,MaxLen:10)
         * numeric(min:5,max:100,notAllowed:[10,20,30,40])
         */
        public function check($value, array $parameters){

            if(
                !is_array($value) ||
                !$value instanceof \ArrayAccess ||
                !$value instanceof \Countable ||
                !$value instanceof \Traversable
            ){
                return false;
            }

            if($this->validateByParameters($value, $parameters)===false){
                return false;
            }

            $parameters = $this->normalizeParameters($parameters,[
                'min' => null,
                'max' => null
            ]);

            if(is_numeric($parameters['min']) && (!isset($count) && $count = count($value))!==null && $count < $parameters['min'] ){
                $this->last_internal_error_message = 'Array is less than '.$parameters['min'].'';
                return false;
            }
            if(is_numeric($parameters['max']) && (!isset($count) && $count = count($value))!==null && $count > $parameters['max'] ){
                $this->last_internal_error_message = 'Array is long than '.$parameters['max'].'';
                return false;
            }

            $hinter = $this->getHinter();
            if(is_array($parameters['verified'])){
                list($like,$nextTypeDefinition) = $parameters['verified'];
            }else{
                $like = $parameters['verified'];
            }

            if(!$like)$like = '[]';
            if(!isset($nextTypeDefinition) || !$nextTypeDefinition || $nextTypeDefinition==='any'){
                $nextTypeDefinition = null;
            }
            switch($like){
                case '[]':
                    if($nextTypeDefinition){
                        foreach ($value as $key => & $v) {
                            if($hinter->check($nextTypeDefinition,$v,true)===false){
                                return false;
                                break;
                            }
                        }
                    }
                    break;
                case '[s]':
                    foreach ($value as $key => & $v) {
                        if(!is_string($key)){
                            $this->last_internal_error_message = 'Array is not full associative indexes';
                            return false;
                            break;
                        }elseif($nextTypeDefinition && $hinter->check($nextTypeDefinition,$v,true)===false){
                            return false;
                            break;
                        }
                    }
                    break;
                case '[i]':
                    foreach ($value as $key => & $v) {
                        if(!is_numeric($key)){
                            $this->last_internal_error_message = 'Array is not full numeric indexes';
                            return false;
                            break;
                        }elseif($nextTypeDefinition && $hinter->check($nextTypeDefinition,$v,true)===false){
                            return false;
                            break;
                        }
                    }
                    break;
            }
            return true;

        }

        /**
         * @param string $type
         *
         * @return bool
         */
        public function verify($type){
            $type = trim(trim($type,'{}'));
            $l = strlen($type);
            if(substr($type,-2)==='[]' && $l > 2) {
                $this->verified = ['[]',substr($type, 0, -2)];
                return true;
            }elseif(substr($type,-3) === '[s]' && $l > 3){
                $this->verified = ['[s]',substr($type, 0, -3)];
                return true;
            }elseif(substr($type,-3) === '[i]' && $l > 3){
                $this->verified = ['[i]',substr($type, 0, -3)];
                return true;
            }else{
                return false;
            }
        }

	}
}

 