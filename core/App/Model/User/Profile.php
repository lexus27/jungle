<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.06.2016
 * Time: 22:43
 */
namespace App\Model\User {
	
	use App\Model\User;
	use Jungle\Data\Record\Collection\Relationship;
	use Jungle\Data\Record\Head\Schema;
	use Jungle\Data\Record\Model;

	/**
	 * Class Profile
	 * @package App\Model\User
	 */
	class Profile extends Model{

		/** @var  string */
		protected $id;


		/** @var  string */
		protected $first_name;

		/** @var  string */
		protected $last_name;

		/** @var  string */
		protected $middle_name;


		/** @var  string */
		protected $mobilephone;

		/** @var  string */
		protected $email;


		/** @var  string */
		protected $country;

		/** @var  string */
		protected $state;

		/** @var  string */
		protected $city;


		/** @var  User */
		protected $user;

		/** @var  Relationship|Contact{} */
		protected $emails;

		/** @var  Relationship|Contact{} */
		protected $mobile_phones;


		/** @var  int */
		protected $birth_on;

		/** @var  int */
		protected $create_on;

		/** @var  int */
		protected $update_on;


		/**
		 * @return string
		 */
		public function getSource(){
			return 'ex_user_profile';
		}

		/**
		 * @return bool
		 */
		public function getAutoInitializeProperties(){
			return true;
		}

		/**
		 * @param Schema $schema
		 */
		public static function initialize(Schema $schema){
			$schema->field('id',[ 'type'  => 'integer', 'readonly'  => true, ]);

			$schema->field('first_name');
			$schema->field('last_name');
			$schema->field('middle_name');

			$schema->field('create_on','date');
			$schema->field('update_on','date');
			$schema->field('birth_on','date');

			$schema->field('country');
			$schema->field('state');
			$schema->field('city');

			$schema->belongsTo('user',User::class,['id'],['id'],[
				'delete_rule' => 'cascade',
			    'update_rule' => 'restrict'
			]);

			$schema->hasMany('contacts',Contact::class,['id'],['user_id']);

			$schema->hasMany('mobile_phones',User\Contact\Mobilephone::class, ['id'],['user_id']);
			$schema->hasMany('emails',User\Contact\Mobilephone::class, ['id'],['user_id']);
		}

		/**
		 *
		 */
		public function beforeCreate(){
			$this->create_on = time();
		}

		/**
		 *
		 */
		public function beforeUpdate(){
			$this->update_on = time();
		}
	}
}

