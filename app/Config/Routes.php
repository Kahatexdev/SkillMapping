<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::index');
$routes->get('/login', 'AuthController::index');
$routes->post('authverify', 'AuthController::login');
$routes->get('/pengguna', 'UserController::index');


$routes->group('/monitoring', ['filter' => 'Monitoring'], function ($routes) {
    $routes->get('', 'MonitoringController::index');
    // $routes->post('inputbagian', 'MonitoringController::inputbagian');
    $routes->get('datakaryawan', 'MonitoringController::karyawan');
    // $routes->post('importkaryawan', 'MonitoringController::importkaryawan');
    $routes->get('dataUser', 'MonitoringController::user');
    $routes->get('dataBagian', 'MonitoringController::bagian');
    $routes->get('dataAbsen', 'MonitoringController::absen');
    $routes->get('dataJob', 'MonitoringController::job');
});