<?php
trait A{

	public function one(){
		parent::one(); echo ' ss';
	}


}
class O{

}
class B extends O{

	use A;
	/**
	 *
	 */
	public function one(){

		echo 'aa ';
		A::one();
	}
}

$b = new B();
$b->one();