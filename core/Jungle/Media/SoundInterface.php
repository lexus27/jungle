<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.07.2016
 * Time: 22:59
 */
namespace Jungle\Media {

	/**
	 * Interface SoundInterface
	 * @package Jungle\Media
	 */
	interface SoundInterface{

		public function setTitle($title);

		public function getTitle();


		public function setAuthor($author);

		public function getAuthor();


		public function setAlbum($album);
		
		public function getAlbum();


		public function setTrackNumber($number);

		public function getTrackNumber();


	}
}

