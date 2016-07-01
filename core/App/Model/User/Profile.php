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
	use Jungle\Data\Record\Head\Field\Relation;
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
		protected $mobilephone;

		/** @var  string */
		protected $state;

		/** @var  string */
		protected $city;

		/** @var  User */
		protected $user;

		/**
		 * @return string
		 */
		public function getSource(){
			return 'ex_user_profile';
		}

		/**
		 *
		 */
		public function initialize(){
			$this->specifyField('id','integer');$this->specifyFieldVisibility('id',true,false);
			$this->specifyField('first_name','string');
			$this->specifyField('last_name','string');
			$this->specifyField('mobilephone','string');
			$this->specifyField('state','string');
			$this->specifyField('city','string');

			$this->belongsTo('user',User::class,Relation::ACTION_CASCADE,Relation::ACTION_CASCADE,false,['id'],['id'],false);
		}

	}
}

