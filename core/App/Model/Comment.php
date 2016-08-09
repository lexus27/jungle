<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.07.2016
 * Time: 18:10
 */
namespace App\Model {
	
	use App\Model\User\Note;
	use Jungle\Data\Record\Head\Schema;
	use Jungle\Data\Record\Model;

	/**
	 * Class Comment
	 * @package App\Model
	 *
	 * @property $id
	 * @property $text
	 * @property $subject
	 * @property $user
	 *
	 */
	class Comment extends Model{

		/** @var  string */
		protected $id;

		/** @var  string */
		protected $subject_schema;

		/** @var  int */
		protected $subject_id;

		/** @var  int */
		protected $user_id;

		/** @var  string */
		protected $text;

		/** @var  User */
		protected $user;

		/** @var  Note */
		protected $subject;


		/**
		 * @return string
		 */
		public function getSource(){
			return 'ex_comment';
		}

		/**
		 * @param Schema $schema
		 */
		public static function initialize(Schema $schema){
			$schema->field('id',[
				'type' => 'integer',
				'readonly' => true,
			]);
			$schema->field('subject_schema',[
				'type' => 'string',
				'readonly' => true,
			]);
			$schema->field('subject_id',[
				'type' => 'integer',
				'readonly' => true,
			]);
			$schema->field('user_id',[
				'type' => 'integer',
				'readonly' => true,
			]);
			$schema->field('text');

			$schema->belongsToDynamic('subject','subject_schema',['subject_id'], ['id'], [
				Note::class
			],[],[
				'delete_rule' => 'cascade',
				'update_rule' => 'cascade',
			]);

			$schema->belongsTo('user',User::class,['user_id'],['id'],[
				'delete_rule' => 'cascade',
				'update_rule' => 'cascade'
			]);

			//$schema->hasManyToMany('memberIn',Member::class,Usergroup::class,['id'],['user_id'],['group_id'],['id']);
		}

		public function getAutoInitializeProperties(){
			return true;
		}
	}
}

