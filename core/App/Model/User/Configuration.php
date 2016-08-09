<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.06.2016
 * Time: 22:47
 */
namespace App\Model\User {
	
	use App\Model\User;
	use Jungle\Data\Record\Head\Schema;
	use Jungle\Data\Record\Model;

	/**
	 * Class Configuration
	 * @package App\Model\User
	 */
	class Configuration extends Model{

		/** @var  string */
		protected $key;

		/** @var  mixed */
		protected $value;

		/** @var  int */
		protected $user_id;

		/** @var  User */
		protected $user;

		/**
		 * @param Schema $schema
		 */
		public static function initialize(Schema $schema){
			$schema->field('key','string');
			$schema->field('value',[
				'type' => 'string',
			    'nullable' => true
			]);
			$schema->field('user_id','int');

			$schema->belongsTo('user',User::class,['user_id'],['id'],[
				'delete_rule' => 'cascade',
				'update_rule' => 'restrict',
			]);

		}

		/**
		 * @return string
		 */
		public function getSource(){
			return 'ex_user';
		}

	}
}

