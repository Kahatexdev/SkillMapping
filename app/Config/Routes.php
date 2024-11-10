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
    // $routes->get('karyawanImport', 'KaryawanController::import');
    $routes->get('downloadTemplateKaryawan', 'KaryawanController::downloadTemplate');
    $routes->post('karyawanStoreImport', 'KaryawanController::upload');
    $routes->get('karyawanCreate', 'KaryawanController::create');
    $routes->post('karyawanStore', 'KaryawanController::store');
    $routes->get('karyawanEdit/(:num)', 'KaryawanController::edit/$1');
    $routes->post('karyawanUpdate/(:num)', 'KaryawanController::update/$1');
    $routes->get('karyawanDelete/(:num)', 'KaryawanController::delete/$1');
    // user
    $routes->get('dataUser', 'MonitoringController::user');
    $routes->get('userCreate', 'UserController::create');
    $routes->post('userStore', 'UserController::store');
    $routes->get('userEdit/(:num)', 'UserController::edit/$1');
    $routes->post('userUpdate/(:num)', 'UserController::update/$1');
    $routes->get('userDelete/(:num)', 'UserController::delete/$1');
    // bagian
    $routes->get('dataBagian', 'MonitoringController::bagian');
    $routes->get('bagianCreate', 'BagianController::create');
    $routes->post('bagianStore', 'BagianController::store');
    $routes->get('bagianEdit/(:num)', 'BagianController::edit/$1');
    $routes->post('bagianUpdate/(:num)', 'BagianController::update/$1');
    $routes->get('bagianDelete/(:num)', 'BagianController::delete/$1');
    // absen
    $routes->get('dataAbsen', 'MonitoringController::absen');
    $routes->get('absenCreate', 'AbsenController::create');
    $routes->post('absenStore', 'AbsenController::store');
    $routes->get('absenEdit/(:num)', 'AbsenController::edit/$1');
    $routes->post('absenUpdate/(:num)', 'AbsenController::update/$1');
    $routes->get('absenDelete/(:num)', 'AbsenController::delete/$1');
    $routes->get('absenImport', 'AbsenController::import');
    $routes->get('downloadTemplateAbsen', 'AbsenController::downloadTemplate');
    $routes->post('absenStoreImport', 'AbsenController::upload');
    $routes->get('absenEmpty', 'AbsenController::empty');
    // jobrole
    $routes->get('dataJob', 'MonitoringController::job');
    $routes->get('jobroleCreate', 'JobroleController::create');
    $routes->post('jobroleStore', 'JobroleController::store');
    $routes->get('jobroleEdit/(:num)', 'JobroleController::edit/$1');
    $routes->post('jobroleUpdate/(:num)', 'JobroleController::update/$1');
    $routes->get('jobroleDelete/(:num)', 'JobroleController::delete/$1');
    // bsmc
    $routes->get('dataBsmc', 'MonitoringController::bsmc');
    $routes->get('downloadTemplateBsmc', 'BsMcController::downloadTemplate');
    $routes->post('bsmcStoreImport', 'BsMcController::upload');
    $routes->get('bsmcCreate', 'BsMcController::create');
    $routes->get('bsmcEdit/(:num)', 'BsMcController::edit/$1');
    $routes->get('bsmcUpdate/(:num)', 'BsMcController::update/$1');
    $routes->get('bsmcDelete/(:num)', 'BsMcController::delete/$1');
    // penilaian
    $routes->get('dataPenilaian', 'MonitoringController::penilaian');
    $routes->post('getAreaUtama', 'PenilaianController::getAreaUtama');
    $routes->post('getArea', 'PenilaianController::getArea');
    $routes->post('getJobRole', 'PenilaianController::getJobRole');
    $routes->get('penilaianCreate', 'PenilaianController::create');
    $routes->post('cekPenilaian', 'PenilaianController::cekPenilaian');
    $routes->post('penilaianStore', 'PenilaianController::store');
});

$routes->group('/mandor', ['filter' => 'Mandor'], function ($routes) {
    $routes->get('', 'MandorController::karyawan');

    $routes->get('dataKaryawan', 'MandorController::karyawan');
    // $routes->get('karyawanImport', 'KaryawanController::import');
    // $routes->get('downloadTemplateKaryawan', 'KaryawanController::downloadTemplate');
    // $routes->post('karyawanStoreImport', 'KaryawanController::upload');
    // $routes->get('karyawanCreate', 'KaryawanController::create');
    // $routes->post('karyawanStore', 'KaryawanController::store');
    // $routes->get('karyawanEdit/(:num)', 'KaryawanController::edit/$1');
    // $routes->post('karyawanUpdate/(:num)', 'KaryawanController::update/$1');
    // $routes->get('karyawanDelete/(:num)', 'KaryawanController::delete/$1');

    $routes->get('dataAbsen', 'MandorController::absen');
    // $routes->get('absenCreate', 'AbsenController::create');
    // $routes->post('absenStore', 'AbsenController::store');
    // $routes->get('absenEdit/(:num)', 'AbsenController::edit/$1');
    // $routes->post('absenUpdate/(:num)', 'AbsenController::update/$1');
    // $routes->get('absenDelete/(:num)', 'AbsenController::delete/$1');
    // $routes->get('absenImport', 'AbsenController::import');
    // $routes->get('downloadTemplateAbsen', 'AbsenController::downloadTemplate');
    // $routes->post('absenStoreImport', 'AbsenController::upload');
    // $routes->get('absenEmpty', 'AbsenController::empty');

    $routes->get('dataPenilaian', 'MandorController::penilaian');
});
