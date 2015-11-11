<?php
/**
 * Created by PhpStorm.
 * Project: MobileCasino
 * Date: 12.03.2015
 * Time: 0:12
 */

namespace Jungle\Basic;


interface ITransient {

	public function setDirty($state = true);

	public function isDirty();

}