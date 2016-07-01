<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.06.2016
 * Time: 22:44
 */
namespace App\Model\User {
	
	use App\Model\User;
	use Jungle\Data\Record\Head\Field\Relation;
	use Jungle\Data\Record\Model;

	/**
	 * Class Note
	 * @package App\Model\User
	 */
	class Note extends Model{

		/** @var  integer */
		protected $id;

		/** @var  integer */
		protected $user_id;

		/** @var  string */
		protected $title;

		/** @var  string */
		protected $text;

		/** @var  User */
		protected $user;


		/**
		 * @return string
		 */
		public function getSource(){
			return 'ex_user_note';
		}

		/**
		 *
		 */
		public function initialize(){
			$this->specifyField('id','integer');$this->specifyFieldVisibility('id',true,false);
			$this->specifyField('user_id','integer');
			$this->specifyField('title','string','header');
			$this->specifyField('text','string','body');

			$this->belongsTo('user',User::class,Relation::ACTION_CASCADE,Relation::ACTION_CASCADE,false,['user_id'],['id'],false);
		}

	}
}

