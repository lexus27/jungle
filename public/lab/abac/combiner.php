<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.09.2016
 * Time: 14:47
 */





$combiner_settings = [

	'delegate' => [
		'default' => 'not_applicable',
		'applicable' => [
			'early'     => true,
			'effect'    => '{current}'
		],
	],

	'delegate_same' => [
		'default' => '{same}',
		'applicable' => [
			'early'     => true,
			'effect'    => '{current}'
		],
	],

	'same' => [
		'default'=> '{same}',
		'not_applicable' => [
			'effect'    => '{current}'
		],
		'applicable' => [
			'check'     => '{!same}',
			'early'     => true,
			'effect'    => '{current}'
		]
	],

	'same_soft' => [
		'default'   => '{same}',
		'empty'     => '{same}',
		'applicable' => [
			'check'     => '{!same}',
			'early'     => true,
			'effect'    => '{current}'
		],

	],

];



$combiner = new Combiner();
$combiner->setSame('deny');
$combiner->setConfig($combiner_settings['delegate_same']);

echo '<p><pre>';
var_dump($combiner->match([
	'not_applicable', 'deny'
]));
echo '</pre></p>';

/**
 * Class Combiner
 *
 */
class Combiner{


	/** @var array  */
	protected $history = [];

	/** @var  string  */
	protected $default_effect = 'not_applicable';

	/** @var  string */
	protected $same_effect;

	/** @var  string */
	protected $fixed_effect;

	/** @var  string */
	protected $current_effect;

	/** @var int  */
	protected $current_iteration;

	/** @var bool  */
	protected $early = false;

	/** @var array  */
	protected $config = [];


	/**
	 * Combiner constructor.
	 * @param array|null $config
	 */
	public function __construct(array $config = null){
		if($config!==null){
			$this->setConfig($config);
		}
	}

	/**
	 * @param $same
	 * @return $this
	 */
	public function setSame($same){
		$this->same_effect = $same;
		return $this;
	}

	/**
	 * Clone for extending.
	 */
	public function __clone(){
		$this->history = [];
		$this->current_iteration = 0;
		$this->current_effect = null;
		$this->fixed_effect = null;
		$this->early = false;
	}

	/**
	 * Reset.
	 */
	public function reset(){
		$this->history = [];
		$this->current_iteration = 0;
		$this->current_effect = null;
		$this->fixed_effect = null;
		$this->early = false;
	}


	/**
	 * @param array $config
	 * @param bool|false $overlap
	 * @return $this
	 */
	public function setConfig(array $config, $overlap = false){
		if(!$this->config || !$overlap){
			$this->config = array_replace_recursive([
				'default'           => null,
				'empty'             => null,
				'result'            => null,
				'history'           => true,
				'not_applicable'    => [
					'check'     => null,
					'early'     => null,
					'effect'    => null,
					'history'   => null,
				],
				'applicable'        => [
					'check'     => null,
					'early'     => null,
					'effect'    => null,
					'history'   => null,
				],
				'deny'              => [
					'check'     => null,
					'early'     => null,
					'effect'    => null,
					'history'   => null,
				],
				'permit'            => [
					'check'     => null,
					'early'     => null,
					'effect'    => null,
					'history'   => null,
				],

			],$config);
		}elseif($this->config && $overlap){
			$this->config = array_replace_recursive($this->config, $config);
		}
		return $this;
	}


	/**
	 * @return bool
	 */
	public function onApplicable(){
		return $this->_eachEffect('applicable');
	}

	/**
	 * @return bool
	 */
	public function onNotApplicable(){
		return $this->_eachEffect('not_applicable');
	}

	/**
	 * @return bool
	 */
	public function onDeny(){
		return $this->_eachEffect('deny');
	}

	/**
	 * @return bool
	 */
	public function onPermit(){
		return $this->_eachEffect('permit');
	}


	/**
	 *
	 */
	public function onEmpty(){
		if(($effect = $this->config['empty'])!==null){
			$this->fixed_effect = $this->_checkoutEffect($effect);
		}
	}

	/**
	 * @return mixed
	 */
	public function getEffect(){
		if($this->fixed_effect === null && $this->config['default']!==null){
			$default = $this->config['default'];
			$this->fixed_effect = $this->_checkoutEffect($default);
		}
		$result = $this->config['result'];
		if($result!==null){
			if(is_callable($result)){
				$this->fixed_effect = call_user_func($result, $this->history, $this->fixed_effect, $this->same_effect, $this->current_effect);
			}
		}
		return $this->fixed_effect===null?$this->default_effect:$this->fixed_effect;
	}


	/**
	 * @param array $aggregation
	 * @return mixed
	 */
	public function match(array $aggregation){
		if($aggregation){
			foreach($aggregation as $i => $effect){
				$this->current_iteration = $i;
				$this->current_effect = $effect;
				switch($effect){
					case 'permit':
						if(!$this->onApplicable()){
							$this->onPermit();
						}
						break;
					case 'deny':
						if(!$this->onApplicable()){
							$this->onDeny();
						}
						break;
					case 'not_applicable':
						$this->onNotApplicable();
						break;
					case 'indeterminate':

						break;
				}
				if($this->early){
					break;
				}
			}
		}else{
			$this->onEmpty();
		}
		return $this->getEffect();
	}



	/**
	 * @param $name
	 * @return bool
	 */
	protected function _eachEffect($name){
		$config = $this->config[$name];
		/**
		 * @var mixed $effect
		 * @var mixed $check
		 * @var bool $early
		 * @var bool $history
		 */
		extract($config);

		$historyEnabled = $history!==null?$history:$this->config['history'];
		if($historyEnabled){
			$this->history[$this->current_iteration] = $this->current_effect;
		}
		if($check){
			if($check === '{same}'){
				if($this->current_effect === $this->same_effect){
					if($early){
						$this->early = true;
					}
					if($effect !== null){
						$this->fixed_effect = $this->_checkoutEffect($effect);
					}
				}
			}elseif($check === '{!same}'){
				if($this->current_effect !== $this->same_effect){
					if($early){
						$this->early = true;
					}
					if($effect !== null){
						$this->fixed_effect = $this->_checkoutEffect($effect);
					}
				}
			}else{
				return false;
			}
			return true;
		}elseif($effect !== null){
			if($early){
				$this->early = true;
			}
			$this->fixed_effect = $this->_checkoutEffect($effect);
		}
		return false;
	}

	/**
	 * @param $effect
	 * @return mixed
	 */
	protected function _checkoutEffect($effect){
		if($effect === '{same}'){
			return $this->same_effect;
		}elseif($effect === '{current}'){
			return $this->current_effect;
		}elseif(is_callable($effect)){
			return call_user_func($effect, $this->history, $this->fixed_effect, $this->same_effect, $this->current_effect);
		}else{
			return $effect;
		}
	}



}