<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.08.2016
 * Time: 11:51
 */
namespace Jungle\User\Session {

	use Jungle\User\Session\Exception\NotFound;
	use Jungle\User\Session\Exception\NotSupplied;
	use Jungle\User\Session\Exception\Overdue;
	use Jungle\User\SessionInterface;

	/**
	 * Interface ProviderInterface
	 * @package Jungle\User\Session
	 */
	interface ProviderInterface{


		/**
		 * @param $signature
		 * @param SessionInterface $session
		 * @return mixed
		 */
		public function onSuccess($signature, SessionInterface $session);

		/**
		 * @param Overdue $exception
		 * @param bool $readAccess
		 * @return mixed
		 */
		public function catchOverdue(Overdue $exception, $readAccess = false);

		/**
		 * @param NotFound $exception
		 * @param bool $readAccess
		 * @return mixed
		 */
		public function catchNotFound(NotFound $exception, $readAccess = false);

		/**
		 * @param NotSupplied $exception
		 * @param bool $readAccess
		 * @return mixed
		 */
		public function catchNotSupplied(NotSupplied $exception, $readAccess = false);

		/**
		 * @return mixed
		 */
		public function hasSignal();

		/**
		 * @param $signature
		 * @return string
		 */
		public function storeSignature($signature);


		/**
		 * @return mixed
		 */
		public function getLifetime();

		/**
		 * @param SignatureInspectorInterface $inspector
		 * @return mixed
		 */
		public function setSignatureInspector(SignatureInspectorInterface $inspector);

		/**
		 * @return mixed
		 */
		public function getSignatureInspector();




		/**
		 * @param StorageInterface $storage
		 * @param bool $readAccess
		 * @return SessionInterface
		 */
		public function requireSession(StorageInterface $storage, $readAccess = false);

	}
}

