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
	
	use App\Model\Comment;
	use App\Model\User;
	use Jungle\Data\Record\Collection\Relationship;
	use Jungle\Data\Record\Head\Field\Relation;
	use Jungle\Data\Record\Head\Schema;
	use Jungle\Data\Record\Model;

	/**
	 * Class Note
	 * @package App\Model\User
	 *
	 * @property $id
	 * @property $user_id
	 * @property $title
	 * @property $text
	 * @property $user
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

		/** @var  Comment[]|Relationship */
		protected $comments;

		/**
		 * @return string
		 */
		public function getSource(){
			return 'ex_user_note';
		}

		/**
		 * @param Schema $schema
		 */
		public static function initialize(Schema $schema){
			$schema->field('id',[
				'type'          => 'integer',
				'readonly'      => true
			]);
			$schema->field('user_id',[
				'type'          => 'integer',
				'readonly'      => true
			]);
			$schema->field('title',[
				'original_key'  => 'header'
			]);
			$schema->field('text',[
				'original_key'  => 'body'
			]);

			$schema->belongsTo('user',User::class,['user_id'],['id'],[
				'delete_rule' => Relation::ACTION_CASCADE,
				'update_rule' => Relation::ACTION_CASCADE,
			]);

			$schema->hasManyDynamic('comments',Comment::class,['id'],['subject_id'],'subject_schema',['subject']);
		}

	}
}

