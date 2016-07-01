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
	use Jungle\Data\Record\Collection\Relationship;
	use Jungle\Data\Record\Model;

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
	class User extends Model{

		/** @var  string */
		protected $id;

		/** @var  string */
		protected $username;

		/** @var  string */
		protected $password;

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
		 * @Do-initialize-current-model-schema
		 */
		public function initialize(){
			$this->specifyField('id','integer');
			$this->specifyField('username','string');
			$this->specifyField('password','string','password_hash');
			$this->specifyFieldVisibility('id',true,false);

			$this->hasOne('profile',Profile::class,['id'],['id'],true);
			$this->hasMany('notes',Note::class,['id'],['user_id'],true);
			//$this->hasManyToMany('memberIn',Member::class,Usergroup::class,['id'],['user_id'],['group_id'],['id']);
		}

	}
}

