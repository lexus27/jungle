<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.07.2016
 * Time: 21:06
 */
namespace App\Model {
	
	use Jungle\Data\Record\Head\Schema;
	use Jungle\Data\Record\Model;
	use Jungle\User\SessionInterface;
	use Jungle\User\SessionTrait;
	use Jungle\User\UserInterface;

	/**
	 * Class Session
	 * @package App\Model
	 *
	 *
	 * @property $id
	 * @property $user_id
	 * @property $data
	 * @property $create_time
	 * @property $modify_time
	 * @property $registered_ip
	 * @property $registered_user_agent
	 *
	 * @property UserInterface|User $user
	 */
	class Session extends Model implements SessionInterface{

		use SessionTrait;

		/** @var  int */
		protected $user_id;

		/** @var  UserInterface|User */
		protected $user;


		public function getSource(){
			return 'ex_session';
		}

		/**
		 * @return array
		 */
		public function getAutoInitializeProperties(){
			return true;
		}

		/**
		 * @param Schema $schema
		 */
		public static function initialize(Schema $schema){
			$schema->field('id');
			$schema->field('user_id',[
				'type'=>'int',
				'nullable' => true,
				'default' => null,
				'readonly' => true
			]);
			$schema->field('data','serialized');
			$schema->field('create_time','date');
			$schema->field('modify_time','date');
			$schema->field('registered_ip');
			$schema->field('registered_user_agent');
			$schema->field('token','bool');
			$schema->field('permanent','bool');
			$schema->field('permissions',[ 'type' => 'serialized', 'nullable' => true, 'default' => null ]);

			$schema->belongsTo('user',User::class,['user_id'],['id'],[
				'delete_rule' => 'cascade',
			    'update_rule' => 'restrict',
			    'nullable' => true,
			]);
		}

		/**
		 * @param UserInterface $user
		 * @return mixed
		 */
		public function setUser(UserInterface $user = null){
			$this->setProperty('user',$user);
			return $this;
		}

		/**
		 * @return UserInterface|null
		 */
		public function getUser(){
			return $this->_getFrontProperty('user');
		}


		/**
		 * @return bool
		 */
		public function hasUser(){
			return $this->user || $this->user_id;
		}

	}
}

