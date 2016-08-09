<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.07.2016
 * Time: 18:27
 */
namespace App\Modules\Index\Controller\User {
	
	use App\Model\Comment;
	use Jungle\Application\Dispatcher\Process;
	use Jungle\Di\Injectable;

	class CommentController extends Injectable{


		/**
		 * @param \Jungle\Application\Dispatcher\Process $process
		 */
		public function indexAction(Process $process){

		}

		/**
		 * @param \Jungle\Application\Dispatcher\Process $process
		 * @return array
		 */
		public function createAction(Process $process){
			if($process->text){
				$comment = new Comment();
				$comment->text = $process->text;
				$comment->subject = $process->subject;
				$comment->user = $process->user;
				$comment->save();
				return [
					'comment' => $comment,
				    'user' => $process->user,
				    'subject' => $process->subject
				];
			}
			return [
				'user' => $process->user,
			    'subject' => $process->subject
			];
		}

		/**
		 * @param \Jungle\Application\Dispatcher\Process $process
		 */
		public function updateAction(Process $process){

		}

		/**
		 * @param \Jungle\Application\Dispatcher\Process $process
		 */
		public function deleteAction(Process $process){

		}

	}
}

