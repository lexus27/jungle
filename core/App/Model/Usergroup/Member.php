<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.06.2016
 * Time: 22:44
 */
namespace App\Model\Usergroup {
	
	use App\Model\User;
	use App\Model\Usergroup;
	use Jungle\Data\Record\Head\Schema;
	use Jungle\Data\Record\Model;

	/**
	 * Class Member
	 * @package App\Model\Usergroup
	 */
	class Member extends Model{

		/** @var  int */
		protected $id;

		/** @var  int */
		protected $user_id;

		/** @var  int */
		protected $group_id;

		/** @var  User */
		protected $user;

		/** @var  Usergroup */
		protected $group;

		/**
		 * @return string
		 */
		public function getSource(){
			return 'ex_usergroup_member';
		}


		/**
		 * @param Schema $schema
		 */
		public static function initialize(Schema $schema){
			$schema->field('id','int');
			$schema->field('user_id','int');
			$schema->field('group_id','int');
			$schema->belongsTo('user',User::class,['user_id'],['id'],[
				'delete_rule' => 'cascade',
				'update_rule' => 'restrict',
			]);
			$schema->belongsTo('group',Usergroup::class,['group_id'],['id'],[
				'delete_rule' => 'cascade',
				'update_rule' => 'restrict',
			]);
		}

	}
}

