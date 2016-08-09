<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.07.2016
 * Time: 20:03
 */
namespace App\Model\User {
	
	use App\Model\User;
	use App\Model\User\Contact\Type;
	use Jungle\Data\Record\Head\Schema;
	use Jungle\Data\Record\Model;
	use Jungle\Messenger\IContact;

	/**
	 * Class Contact
	 * @package App\Model\User
	 */
	class Contact extends Model implements IContact{

		/** @var  int */
		protected $id;

		/** @var  Type */
		protected $type;

		/** @var  User */
		protected $user;

		/** @var  string */
		protected $address;

		/** @var  bool */
		protected $is_default;

		/** @var  int */
		protected $user_id;

		/** @var  int */
		protected $type_id;



		/**
		 * @return string
		 */
		public function getSource(){
			return 'ex_user_profile_contact';
		}

		/**
		 * @param Schema $schema
		 */
		public static function initialize(Schema $schema){

			$schema->field('id','bool');
			$schema->field('user_id','int');
			$schema->field('type_id','int');
			$schema->field('address','string');
			$schema->field('is_default','bool');

			$schema->belongsTo('user', User::class,['user_id'],['id'],[
				'delete_rule' => 'cascade',
				'update_rule' => 'restrict'
			]);
		}

		/**
		 * @param string $address
		 * @return $this
		 */
		public function setAddress($address){
			$this->address = $address;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getAddress(){
			return $this->address;
		}
	}
}

