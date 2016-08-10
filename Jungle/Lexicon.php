<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.07.2016
 * Time: 21:13
 */
namespace Jungle {
	
	use Jungle\Lexicon\Locale;

	/**
	 * Class Lexicon
	 * @package Jungle
	 */
	class Lexicon{

		/** @var  string */
		protected $locale_delimiter = '_';


		/**
		 * @param $localeString
		 * @return Locale
		 */
		public function parseLocale($localeString){
			preg_match('@([[:alpha:]]{2})(?:[\-_]([[:alpha:]]{2}))?@P',$localeString, $matches);

			$language_code = isset($matches[0])?$matches[0]:null;
			$region_code = isset($matches[1])?$matches[1]:null;

			$locale = new Locale();
			$locale->setLanguageCode($language_code);
			$locale->setRegionCode($region_code);

			return $locale;
		}


		/**
		 * @param Locale $locale
		 * @return string
		 */
		public function renderLocale(Locale $locale){
			return $locale->getLanguageCode() . $this->locale_delimiter . $locale->getRegionCode();
		}

	}
}

