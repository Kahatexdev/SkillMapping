<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::index');
$routes->get('/login', 'AuthController::index');
$routes->post('authverify', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');
// $routes->get('/pengguna', 'UserController::index');


$routes->group('/monitoring', ['filter' => 'Monitoring'], function ($routes) {
    $routes->get('', 'MonitoringController::index');
    // $routes->post('inputbagian', 'MonitoringController::inputbagian');
    $routes->get('datakaryawan', 'MonitoringController::karyawan');
    $routes->get('karyawanImport', 'KaryawanController::import');
    $routes->get('downloadTemplateKaryawan', 'KaryawanController::downloadTemplate');
    $routes->post('karyawanStoreImport', 'KaryawanController::upload');
    $routes->get('karyawanEmpty', 'KaryawanController::empty');
    $routes->get('dataUser', 'MonitoringController::user');
    $routes->get('userCreate', 'UserController::create');
    $routes->post('userStore', 'UserController::store');
    $routes->get('userEdit/(:num)', 'UserController::edit/$1');
    $routes->post('userUpdate/(:num)', 'UserController::update/$1');
    $routes->get('userDelete/(:num)', 'UserController::delete/$1');
    $routes->get('dataBagian', 'MonitoringController::bagian');
    $routes->get('bagianCreate', 'BagianController::create');
    $routes->post('bagianStore', 'BagianController::store');
    $routes->get('bagianEdit/(:num)', 'BagianController::edit/$1');
    $routes->post('bagianUpdate/(:num)', 'BagianController::update/$1');
    $routes->get('dataAbsen', 'MonitoringController::absen');
    $routes->get('dataJob', 'MonitoringController::job');
    $routes->get('jobroleCreate', 'JobroleController::create');
    $routes->post('jobroleStore', 'JobroleController::store');
    $routes->get('jobroleEdit/(:num)', 'JobroleController::edit/$1');
    $routes->post('jobroleUpdate/(:num)', 'JobroleController::update/$1');
    $routes->get('jobroleDelete/(:num)', 'JobroleController::delete/$1');
    $routes->get('absenCreate', 'AbsenController::create');
    $routes->post('absenStore', 'AbsenController::store');
    $routes->get('absenEdit/(:num)', 'AbsenController::edit/$1');
    $routes->post('absenUpdate/(:num)', 'AbsenController::update/$1');
    $routes->get('absenDelete/(:num)', 'AbsenController::delete/$1');
    $routes->get('absenImport', 'AbsenController::import');
    $routes->get('downloadTemplateAbsen', 'AbsenController::downloadTemplate');
    $routes->post('absenStoreImport', 'AbsenController::upload');
    $routes->get('absenEmpty', 'AbsenController::empty');
});