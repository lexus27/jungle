<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.11.2016
 * Time: 20:09
 */
namespace data_orm_test;

use Jungle\Data\Record\Field\Field;
use Jungle\Data\Record\Relation\RelationForeign;
use Jungle\Data\Record\Relation\RelationMany;
use Jungle\Data\Record\Schema\Schema;
use Jungle\Data\Record\Validation\CheckPresenceOf;


include '_boot.php';



class Company extends AbstractModel{

	public $name;

	public $description;


	public static function initialize(Schema $schema){
		$schema->setSource('company');

		$schema->set(new CheckPresenceOf([ 'name', 'description' ]));

		$schema->set(new Field('name'));
		$schema->set(new Field('description'));

		$schema->set(new RelationMany('employees', CompanyEmployee::class.'.company')); // to employees
	}

}
class CompanyEmployeeRole extends AbstractModel{

	public $key;

	public $title;


	public static function initialize(Schema $schema){
		$schema->setSource('employee_role');

		$schema->set(new Field('key'));
		$schema->set(new Field('title'));
	}

}
class CompanyEmployee extends AbstractModel{

	public $company_id;

	public $role_id;


	public static function initialize(Schema $schema){

		$schema->setSource('employee');


		$schema->set(new CheckPresenceOf([ 'role_id' ]));


		$schema->set(new Field('company_id'));
		$schema->set(new Field('role_id'));

		$schema->set(new RelationForeign('company','company_id','id',Company::class,'restrict','cascade'));
		$schema->set(new RelationForeign('role','role_id','id',CompanyEmployeeRole::class,'restrict','cascade'));
	}

}


function company_create(){
	$company = new Company();
	$company->name = uniqid('company_');
	$company->description = 'blah blah';

	$employee = new CompanyEmployee();
	$employee->role_id = 1;
	$company->addRelated('employees', $employee);

	$employee = new CompanyEmployee();
	$company->addRelated('employees', $employee);

	$employee = new CompanyEmployee();
	$employee->role_id = 3;
	$company->addRelated('employees', $employee);

	$company->save();
}
function company_update(){
	$company = Company::findFirst(1);

	$employees = $company->getRelated('employees');
	$employee = $employees[0];
	$employee->role_id = 231;
	//$employee->save();
	// чтобы $company->save() сохранил эмплоя,
	// нужно чтобы объект компании сохранял в цикле каждый загруженный связанный объект

	// иначе требуется внешнее обертывание этого куска кода в транзакцию
	// чтобы в случае ошибки на сохранении компании после эмплоя, эмплой не остался
	$company->save();
}
company_create();