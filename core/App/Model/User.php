<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.06.2016
 * Time: 22:43
 */
namespace App\Model {

	use App\Model\User\Note;
	use App\Model\User\Profile;
	use App\Model\Usergroup\Member;
	use Jungle\Data\Record\Collection\Relationship;
	use Jungle\Data\Record\Head\Schema;
	use Jungle\Data\Record\Model;
	use Jungle\User\UserInterface;

	/**
	 * Class User
	 * @package App\Model
	 *
	 * @property int            $id
	 * @property string         $username
	 * @property string         $password
	 * @property Profile        $profile
	 * @property Note[]         $notes
	 * @property Usergroup[]    $memberIn
	 */
	class User extends Model implements UserInterface{

		/** @var  string */
		protected $id;

		/** @var  string */
		protected $username;

		/** @var  string */
		protected $password;

		/** @var  string */
		protected $salt;

		/** @var  Profile */
		protected $profile;

		/** @var  Relationship|Note[] */
		protected $notes;

		/** @var  Relationship|Usergroup[] */
		protected $memberIn;

		/**
		 * @return string
		 */
		public function getSource(){
			return 'ex_user';
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
			$schema->field('id',[
				'type' => 'integer',
			    'readonly' => true,
			]);
			$schema->field('username');
			$schema->field('password',[ 'original_key' => 'password_hash' ]);

			$schema->hasOne('profile',Profile::class, ['id'], ['id']);
			$schema->hasMany('notes',Note::class, ['id'], ['user_id']);

			$schema->hasManyToMany('memberIn',Member::class,Usergroup::class,['id'],['user_id'],['group_id'],['id']);
		}

		/**
		 * @return mixed
		 */
		public function getId(){
			return $this->id;
		}

		/**
		 * @return mixed
		 */
		public function getUsername(){
			return $this->username;
		}

	}
}