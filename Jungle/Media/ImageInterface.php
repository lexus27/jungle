<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.07.2016
 * Time: 22:49
 */
namespace Jungle\Media {

	/**
	 * Interface ImageInterface
	 * @package Jungle\Media
	 */
	interface ImageInterface{

		const SIDE_TOP      = 'top';
		const SIDE_BOTTOM   = 'bottom';
		const SIDE_LEFT     = 'left';
		const SIDE_RIGHT    = 'right';

		public function getHeight();

		public function getWidth();

		public function getSize();

		/**
		 * @param int $quality
		 * @return mixed
		 */
		public function compress($quality = 100);

		public function rotate($angle);

		public function drawText($text);

		public function drawImage(ImageInterface $image);
		
		public function canvasZoom($percent);
		
		public function setCanvasHeight($pixels);
		
		public function setCanvasWidth($pixels);
		
		/**
		 * @return string|array '16:4' proportion
		 */
		public function getProportion();

		public function proportional($on = true);

		public function setHeight($pixels);

		public function setWidth($pixels);
		
		public function reduceByFrame($on);

		public function fadeOut($percent);

		public function fadeIn($percent);

	}
}

