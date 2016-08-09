<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.07.2016
 * Time: 7:39
 */
namespace App\Modules\Index\Controller\User {

	use App\Model\User;
	use Jungle\Application\Dispatcher\Process;
	use Jungle\Di\Injectable;

	/**
	 * Class NoteController
	 * @package App\Modules\Index\Controller\User
	 *
	 * В данном контроллере, мы наблюдаем C.R.U.D спецефичные действия, для объекта модели!
	 *
	 */
	class NoteController extends Injectable{

		/**
		 * Создать
		 * @param \Jungle\Application\Dispatcher\Process $process
		 * @return User\Note
		 */
		public function createAction(Process $process){
			if($process->title){
				$note = new User\Note();
				$note->assign($process->getParams(),[
					'title',
					'text',
					'user'
				]);
				$note->save();
				return [
					'note' => $note
				];
			}
			return null;
		}

		/**
		 * Обновить
		 * @param \Jungle\Application\Dispatcher\Process $process
		 * @return bool
		 */
		public function updateAction(Process $process){
			/** @var User\Note $note */
			$params = $process->getParams();
			if(isset($params['title'],$params['text'])){
				$note = $process->note;
				return $note->assign($process->getParams(),['title','text'])->save();
			}
			return $process->note;
		}

		/**
		 * Удалить
		 * @param Process $process
		 * @return bool
		 */
		public function deleteAction(Process $process){
			return $process->note->delete();
		}

	}
}

