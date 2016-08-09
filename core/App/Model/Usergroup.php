<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.06.2016
 * Time: 22:44
 */
namespace App\Model {
	
	use App\Model\Usergroup\Member;
	use Jungle\Data\Record\Collection\Relationship;
	use Jungle\Data\Record\Head\Schema;
	use Jungle\Data\Record\Model;

	/**
	 * Class Usergroup
	 * @package App\Model
	 */
	class Usergroup extends Model{

		/** @var  int */
		protected $id;

		/** @var  string */
		protected $name;

		/** @var  Relationship|User[] */
		protected $users;

		/**
		 * @param Schema $schema
		 */
		public static function initialize(Schema $schema){
			$schema->field('id','int');
			$schema->field('name','string');
			$schema->hasManyToMany('users',Member::class,User::class,['id'],['group_id'],['user_id'],['id']);
		}

		/**
		 * @return string
		 */
		public function getSource(){
			return 'ex_usergroup';
		}

	}
}

