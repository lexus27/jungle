<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 11.05.2015
 * Time: 15:14
 */

namespace Jungle\XPlate\CSS\Media {

	use Jungle\Util\INamed;

	/**
	 * Interface ICssMediaType
	 * @package Jungle\XPlate\Interfaces
	 *
	 * Медиа тип это screen all и так далее
	 * @media MediaType and mediaFn and mediaFn
	 *
	 */
	interface IType extends INamed{

		public function __toString();

	}

}