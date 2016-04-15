<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 06.01.2016
 * Time: 0:48
 */
namespace Jungle\Communication {

	use Jungle\Communication\Stream\Builder;
	use Jungle\Communication\Stream\Command;
	use Jungle\Communication\Stream\Connection;
	use Jungle\Communication\Stream\Exception;
	use Jungle\Communication\Stream\Specification;

	/**
	 * Class Stream
	 * @package Jungle\Communication
	 */
	class Stream{

		/** @var Connection */
		protected $connection;

		/** @var Specification */
		protected $specification;

		/** @var string */
		protected $data = '';

		/** @var Command[] */
		protected $executed_commands = [];

		/** @var  array|null commands execute after connect() */
		protected $start;

		/** @var bool  */
		protected $start_executed = false;

		/**
		 * @param Connection|null $connection
		 */
		public function __construct(Connection $connection = null){
			if($connection){
				$this->setConnection($connection);
			}
		}

		/**
		 * @param Connection $connector
		 * @return $this
		 */
		public function setConnection(Connection $connector){
			$this->connection = $connector;
			return $this;
		}

		/**
		 * @return Connection
		 */
		public function getConnection(){
			return $this->connection;
		}

		/**
		 * Стартовый набор комманд который будет выполнятся сразу после каждого соединения с сервером
		 * @param $commands
		 * @return $this
		 */
		public function setStart($commands){
			$this->start = $commands;
			return $this;
		}

		/**
		 * @param \Jungle\Communication\Stream\Specification $specification
		 * @return $this
		 */
		public function setSpecification(Specification $specification){
			$this->specification = $specification;
			return $this;
		}

		/**
		 * @return \Jungle\Communication\Stream\Specification
		 */
		public function getSpecification(){
			return $this->specification;
		}

		/**
		 * @param int $length
		 * @return string
		 */
		public function read($length = 512){
			return $this->connection->read($length, $this->specification->getReader());
		}

		/**
		 * @param $data
		 */
		public function send($data){
			if(is_string($data)){
				$this->data = '';
				$this->connection->send($data);
			}else{
				$this->execute($data);
			}
		}

		/**
		 * @param Builder $builder
		 */
		public static function setBuilder(Builder $builder){
			Builder::setDefault($builder);
		}

		/**
		 * @return Builder
		 */
		public static function getBuilder(){
			return Builder::getDefault();
		}

		/**
		 * @param mixed $definition
		 * @return $this
		 * @throws Exception
		 */
		public function execute($definition){
			if(($spec = $this->getSpecification())){

				if(!$this->start_executed && $this->start){
					$this->start_executed = true;
					$this->execute($this->start);
				}

				$maxLength      = $spec->getMaxLength();
				$composition    = Builder::getDefault()->buildCommandComposition($definition);

				try{
					foreach($composition as $command){
						$command->reset();
						$command->setSpecification($spec);
						if(!$command->isMutable()){
							$this->send($command->represent());
						}
						$this->executed_commands[] = $command;
						$response = $this->read($maxLength);
						$command->setResponse($response);
						$code = $spec->getCode($command->getResponse());

						$command->setCode($code);

						$command->check();
					}
				}catch(Exception $e){
					throw $e;
				}

			}else{
				throw new \LogicException('Specification is not set');
			}
			return $this;
		}

		/**
		 * @return $this
		 */
		public function reset(){
			$this->start_executed       = false;
			$this->executed_commands    = [];
			$this->data                 = '';
			$this->connection->reconnect();
			return $this;
		}

		/**
		 * Клонирование потока,
		 * Спецификация и настройки остаются унаследованными,
		 * Создается новое соединение посредством копирования старого, с его настройками.
		 * Клонирование дает возможность унаследования всех базовых настроек в новом клоне.
		 */
		public function __clone(){
			$this->executed_commands    = [];
			$this->data                 = '';
			$this->connection           = clone $this->connection;
		}

		/**
		 * @return Command[]
		 */
		public function getExecutedCommands(){
			return $this->executed_commands;
		}


	}

}

