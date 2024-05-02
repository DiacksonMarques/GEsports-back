<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
//User
$routes->get('users', 'UserController::getAll', ['filter' => 'auth']);
$routes->get('user', 'UserController::getOne', ['filter' => 'auth']);
$routes->post('user', 'UserController::create', ['filter' => 'auth']);
$routes->put('user/(:segment)', 'UserController::update/$1', ['filter' => 'auth']);

//School
$routes->get('schools', 'SchoolController::getAll');
$routes->get('school/(:segment)', 'SchoolController::getOne/$1', ['filter' => 'auth']);
$routes->post('school', 'SchoolController::create', ['filter' => 'auth']);
$routes->put('school/(:segment)', 'SchoolController::update/$1', ['filter' => 'auth']);
$routes->delete('school/(:segment)', 'SchoolController::delete/$1', ['filter' => 'auth']);

//Responsible
$routes->get('responsibles', 'ResponsibleController::getAll', ['filter' => 'auth']);
$routes->get('responsible/(:segment)', 'ResponsibleController::getOne/$1', ['filter' => 'auth']);
$routes->post('responsible', 'ResponsibleController::create', ['filter' => 'auth']);
$routes->put('responsible/(:segment)', 'ResponsibleController::update/$1', ['filter' => 'auth']);
$routes->delete('responsible/(:segment)', 'ResponsibleController::delete/$1', ['filter' => 'auth']);

//Person
$routes->get('persons', 'PersonController::getAll', ['filter' => 'auth']);
$routes->get('person/(:segment)', 'PersonController::getOne/$1', ['filter' => 'auth']);
$routes->post('person', 'PersonController::create');
$routes->put('person/(:segment)', 'PersonController::update/$1', ['filter' => 'auth']);
$routes->delete('person/(:segment)', 'PersonController::delete/$1', ['filter' => 'auth']);

//Athlete
$routes->get('athletes', 'AthleteController::getAll', ['filter' => 'auth']);
$routes->get('athlete/(:segment)', 'AthleteController::getOne/$1', ['filter' => 'auth']);
$routes->post('athlete', 'AthleteController::create', ['filter' => 'auth']);
$routes->put('athlete/(:segment)', 'AthleteController::update/$1', ['filter' => 'auth']);
$routes->delete('athlete/(:segment)', 'AthleteController::delete/$1', ['filter' => 'auth']);

//Frequency
$routes->get('frequencys', 'FrequencyController::getAll', ['filter' => 'auth']);
$routes->get('frequency/(:segment)', 'FrequencyController::getOne/$1', ['filter' => 'auth']);
$routes->post('frequency', 'FrequencyController::create', ['filter' => 'auth']);
$routes->put('frequency/(:segment)', 'FrequencyController::update/$1', ['filter' => 'auth']);
$routes->delete('frequency/(:segment)', 'FrequencyController::delete/$1', ['filter' => 'auth']);

//Presence
$routes->get('presences', 'PresenceController::getAll', ['filter' => 'auth']);
$routes->get('presence/(:segment)', 'PresenceController::getOne/$1', ['filter' => 'auth']);
$routes->post('presence', 'PresenceController::create', ['filter' => 'auth']);
$routes->put('presence/(:segment)', 'PresenceController::update/$1', ['filter' => 'auth']);
$routes->delete('presence/(:segment)', 'PresenceController::delete/$1', ['filter' => 'auth']);

//Monthly Fee
$routes->get('monthlyFees', 'MonthlyFeeController::getAll', ['filter' => 'auth']);
$routes->get('monthlyFee/(:segment)', 'MonthlyFeeController::getOne/$1', ['filter' => 'auth']);
$routes->post('monthlyFee', 'MonthlyFeeController::create', ['filter' => 'auth']);
$routes->put('monthlyFee/(:segment)', 'MonthlyFeeController::update/$1', ['filter' => 'auth']);
$routes->delete('monthlyFee/(:segment)', 'MonthlyFeeController::delete/$1', ['filter' => 'auth']);

//Form Payment
$routes->get('formPayments', 'FormPaymentController::getAll', ['filter' => 'auth']);
$routes->get('formPayment/(:segment)', 'FormPaymentController::getOne/$1', ['filter' => 'auth']);
$routes->post('formPayment', 'FormPaymentController::create', ['filter' => 'auth']);
$routes->put('formPayment/(:segment)', 'FormPaymentController::update/$1', ['filter' => 'auth']);
$routes->delete('formPayment/(:segment)', 'FormPaymentController::delete/$1', ['filter' => 'auth']);

//Monthly Payment
$routes->get('monthlyPayments', 'MonthlyPaymentController::getAll', ['filter' => 'auth']);
$routes->get('monthlyPayment/(:segment)', 'MonthlyPaymentController::getOne/$1', ['filter' => 'auth']);
$routes->post('monthlyPayment', 'MonthlyPaymentController::create', ['filter' => 'auth']);
$routes->put('monthlyPayment/(:segment)', 'MonthlyPaymentController::update/$1', ['filter' => 'auth']);
$routes->delete('monthlyPayment/(:segment)', 'MonthlyPaymentController::delete/$1', ['filter' => 'auth']);

//Payment
$routes->get('payments', 'PaymentController::getAll', ['filter' => 'auth']);
$routes->get('paymentUser', 'PaymentController::getPaymentsUser', ['filter' => 'auth']);
$routes->get('payment/(:segment)', 'PaymentController::getOne/$1', ['filter' => 'auth']);
$routes->post('payment', 'PaymentController::create', ['filter' => 'auth']);
$routes->put('payment/(:segment)', 'PaymentController::update/$1', ['filter' => 'auth']);
$routes->delete('payment/(:segment)', 'PaymentController::delete/$1', ['filter' => 'auth']);

//Roles
$routes->get('roles', 'RolesController::getAll', ['filter' => 'auth']);
$routes->get('role/(:segment)', 'RolesController::getOne/$1', ['filter' => 'auth']);
$routes->post('role', 'RolesController::create', ['filter' => 'auth']);
$routes->put('role/(:segment)', 'RolesController::update/$1', ['filter' => 'auth']);
$routes->delete('role/(:segment)', 'RolesController::delete/$1', ['filter' => 'auth']);

//Category
$routes->get('categorys', 'CategoryController::getAll', ['filter' => 'auth']);
$routes->get('category/(:segment)', 'CategoryController::getOne/$1', ['filter' => 'auth']);
$routes->post('category', 'CategoryController::create', ['filter' => 'auth']);
$routes->put('category/(:segment)', 'CategoryController::update/$1', ['filter' => 'auth']);
$routes->delete('category/(:segment)', 'CategoryController::delete/$1', ['filter' => 'auth']);
$routes->get('categorysEnrollment', 'CategoryController::getAll');

//Login
$routes->post('login', 'UserController::login');
$routes->post('logout', 'UserController::logout');
$routes->post('checkToken', 'UserController::checkToken');

//Store
$routes->get('citys', 'StoreController::getAllCitys');
$routes->get('donwloadTerm', 'StoreController::dowloadTerm');
$routes->get('donwloadRegulation', 'StoreController::dowloadRegulation');
$routes->get('menus/(:segment)', 'StoreController::getTypeMenus/$1', ['filter' => 'auth']);


$routes->post('personEnrollment', 'EnrolmentController::createEnrollment');
$routes->get('personEnrollment/(:segment)', 'EnrolmentController::getEnrollment/$1');
$routes->get('personEnrollmentAthelete/(:segment)', 'EnrolmentController::getEnrollmentAthelete/$1');
$routes->put('personEnrollment/(:segment)', 'EnrolmentController::updateEnrollment/$1');

//Championship
$routes->post('team', 'ChampionshipController::createTeam');
$routes->get('teams', 'ChampionshipController::allTeams');
