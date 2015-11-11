<?php
/**
 * Created by PhpStorm.
 * Project: MobileCasino
 * Date: 17.03.2015
 * Time: 6:12
 */

namespace Jungle\Basic;


interface IObservable {

	public function addListener($eName,\Closure $fn ,array $options = []);

	public function removeListener($eName,\Closure $fn);

	public function fireEvent($eName);

	public function purgeListeners($eName);

	public function searchEvent($eName);

	public function searchListener($eName,\Closure $fn);

}