/**
 * Created by Alexey on 09.11.2015.
 */
class Class1{

	public static regExp: RegExp = /^Y/;

	/** @type {number} */
	private var1:ArrayBuffer;

	public constructor(string){

	}

}
class Class2 extends Class1{


}
Class2.regExp.test('Y');