<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::index');
$routes->get('/login', 'AuthController::index');
$routes->post('authverify', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

// $routes->get('TrainingSchool/conversation/(:num)/(:num)', 'ChatController::fetchConversation/$1/$2');
// $routes->post('TrainingSchool/send-message', 'ChatController::sendMessage');
// $routes->get('TrainingSchool/getContacts/(:num)', 'ChatController::getContacts/$1');


// $routes->get('/pengguna', 'UserController::index');

$routes->group('/Monitoring', ['filter' => 'Monitoring'], function ($routes) {
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
    $routes->get('exportKaryawan/', 'KaryawanController::exportAll');
    // batch
    $routes->get('dataBatch', 'MonitoringController::batch');
    $routes->get('batchCreate', 'BatchController::create');
    $routes->post('batchStore', 'BatchController::store');
    $routes->get('batchEdit/(:num)', 'BatchController::edit/$1');
    $routes->post('batchUpdate/(:num)', 'BatchController::update/$1');
    $routes->get('batchDelete/(:num)', 'BatchController::delete/$1');
    // periode
    $routes->get('dataPeriode', 'MonitoringController::periode');
    $routes->get('periodeCreate', 'PeriodeController::create');
    $routes->post('periodeStore', 'PeriodeController::store');
    $routes->get('periodeEdit/(:num)', 'PeriodeController::edit/$1');
    $routes->post('periodeUpdate/(:num)', 'PeriodeController::update/$1');
    $routes->get('periodeDelete/(:num)', 'PeriodeController::delete/$1');
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
    $routes->get('absenReport', 'AbsenController::absenReport');
    $routes->get('absenEmpty', 'AbsenController::empty');
    // jobrole
    $routes->get('dataJob', 'MonitoringController::job');
    $routes->get('coba', 'JobroleController::index');
    $routes->get('jobroleCreate', 'JobroleController::create');
    $routes->post('jobroleStore', 'JobroleController::store');
    $routes->get('jobroleEdit/(:num)', 'JobroleController::edit/$1');
    $routes->post('jobroleUpdate/(:num)', 'JobroleController::update/$1');
    $routes->get('jobroleDelete/(:num)', 'JobroleController::delete/$1');
    // bsmc
    $routes->get('dataBsmc', 'MonitoringController::bsmc');
    $routes->get('dataBsmc/(:segment)', 'BsMcController::tampilPerBatch/$1');
    $routes->get('filterBsmc/(:segment)', 'BsMcController::filterBsmc/$1');
    $routes->post('filterBsmc/(:segment)', 'BsMcController::filterBsmc/$1');
    // $routes->get('reportSummaryBsmc/(:segment)/(:num)', 'BsMcController::summaryBsmc/$1/$2');
    $routes->get('reportSummaryBsmc/(:segment)/(:num)', 'BsMcController::sumBsMesin/$1/$2');
    $routes->get('downloadTemplateBsmc', 'BsMcController::downloadTemplate');
    // $routes->post('bsmcStoreImport', 'BsMcController::upload');
    $routes->post('bsmcStoreImport', 'BsMcController::import');
    $routes->get('bsmcCreate', 'BsMcController::create');
    $routes->get('fetchDataBsMc', 'BsMcController::fetchDataAPI');
    $routes->get('bsmcEdit/(:num)', 'BsMcController::edit/$1');
    $routes->get('bsmcUpdate/(:num)', 'BsMcController::update/$1');
    $routes->get('bsmcDelete/(:num)', 'BsMcController::delete/$1');
    // summary rosso
    $routes->get('dataRosso', 'MonitoringController::rosso');
    $routes->get('dataRosso/(:segment)', 'SummaryRossoController::tampilPerBatch/$1');
    $routes->get('summaryRosso', 'SummaryRossoController::summaryRosso');
    $routes->get('downloadTemplateRosso', 'SummaryRossoController::downloadTemplate');
    $routes->post('rossoStoreImport', 'SummaryRossoController::import');
    $routes->get('rossoCreate', 'SummaryRossoController::create');
    $routes->post('rossoStore', 'SummaryRossoController::store');
    $routes->get('rossoEdit/(:num)', 'SummaryRossoController::edit/$1');
    $routes->post('rossoUpdate/(:num)', 'SummaryRossoController::update/$1');
    $routes->get('rossoDelete/(:num)', 'SummaryRossoController::delete/$1');
    $routes->get('reportSummaryRosso/(:segment)/(:num)', 'SummaryRossoController::excelSummaryRosso/$1/$2');
    $routes->get('rossoDetail/(:num)', 'SummaryRossoController::show/$1');
    $routes->get('filterRosso/(:segment)', 'SummaryRossoController::filterRosso/$1');
    $routes->post('filterRosso/(:segment)', 'SummaryRossoController::filterRosso/$1');
    // summary jarum
    $routes->get('dataJarum', 'MonitoringController::jarum');
    $routes->get('dataJarum/(:segment)', 'JarumController::tampilPerBatch/$1');
    $routes->get('summaryJarum', 'JarumController::summaryJarum');
    $routes->get('downloadTemplateJarum', 'JarumController::downloadTemplate');
    $routes->get('filterJarum/(:segment)', 'JarumController::filterJarum/$1');
    $routes->post('filterJarum/(:segment)', 'JarumController::filterJarum/$1');
    // $routes->post('jarumStoreImport', 'JarumController::upload');
    $routes->post('jarumStoreInput', 'JarumController::upload');
    $routes->post('getMontirByArea', 'MonitoringController::getMontirByArea');
    $routes->get('reportSummaryJarum/(:segment)/(:num)', 'JarumController::excelSummaryJarum/$1/$2');

    // penilaian
    $routes->get('dataPenilaian', 'MonitoringController::penilaian');
    $routes->get('getAreaUtama', 'PenilaianController::getAreaUtama');
    $routes->get('getArea', 'PenilaianController::getArea');
    $routes->get('getJobRole', 'PenilaianController::getJobRole');
    $routes->get('getKaryawan', 'PenilaianController::getKaryawan');
    $routes->post('penilaianCreate', 'PenilaianController::create');
    $routes->post('cekPenilaian', 'PenilaianController::cekPenilaian');
    $routes->post('penilaianStore', 'PenilaianController::store');
    $routes->get('penilaianDetail/(:num)/(:num)/(:num)', 'PenilaianController::show/$1/$2/$3');
    $routes->get('penilaianExcel/(:num)/(:num)/(:num)', 'PenilaianController::reportExcel/$1/$2/$3');

    $routes->get('reportBatch/(:segment)', 'PenilaianController::reportAreaperBatch/$1');
    $routes->get('reportPenilaian', 'MonitoringController::reportpenilaian');
    $routes->get('reportBatch', 'MonitoringController::reportBatch');
    $routes->get('reportPenilaian/(:segment)', 'PenilaianController::penilaianPerArea/$1');
    $routes->get('reportPenilaian/(:segment)/(:num)', 'PenilaianController::penilaianPerPeriode/$1/$2');
    $routes->get('reportPenilaian/(:segment)/(:segment)/(:segment)', 'PenilaianController::excelReportPerPeriode/$1/$2/$3');
    $routes->get('reportExcel/(:segment)/(:segment)/(:segment)', 'PenilaianController::reportExcel/$1/$2/$3');
    // http://localhost:8080/Monitoring/exelReportBatch/3/KK1
    $routes->get('exelReportBatch/(:num)/(:segment)', 'PenilaianController::exelReportBatch/$1/$2');

    // routes/web.php atau routes.php (tergantung pada versi CodeIgniter)
    $routes->get('contacts', 'ChatController::getContactsWithLastMessage');
    $routes->get('chat', 'TrainingSchoolController::chat');

    $routes->get('conversation/(:num)/(:num)', 'ChatController::fetchConversation/$1/$2');
    $routes->post('send-message', 'ChatController::sendMessage');
    $routes->get('getContacts/(:num)', 'ChatController::getContacts/$1');
    $routes->post('mark-messages-as-read/(:num)', 'ChatController::markMessagesAsRead/$1');
    $routes->get('count-unread-messages', 'ChatController::countUnreadMessages');
    $routes->get('check-new-messages', 'ChatController::checkNewMessages');
    $routes->get('long-poll-new-messages', 'ChatController::longPollNewMessages'); // Untuk long polling

    $routes->get('cekPenilaian', 'MonitoringController::cekPenilaian');
    $routes->get('historyPindahKaryawan', 'MonitoringController::historyPindahKaryawan');
    $routes->get('reportHistoryPindahKaryawan', 'HistoryPindahKaryawanController::reportExcel');

    $routes->get('evaluasiKaryawan/(:any)/(:any)', 'MandorController::getEmployeeEvaluationStatus/$1/$2');

    $routes->get('updateGradeAkhirPerPeriode', 'PenilaianController::updateGradeAkhirPerPeriode');
});

$routes->group('/Mandor', ['filter' => 'Mandor'], function ($routes) {
    $routes->get('', 'MandorController::dashboard');
    // $routes->get('', 'MonitoringController::index');
    $routes->get('dataKaryawan', 'MandorController::listArea');
    $routes->get('dataKaryawan/(:any)', 'MandorController::detailKaryawanPerArea/$1');

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
    $routes->get('getAreaUtama', 'MandorController::getAreaUtama');
    $routes->get('getArea', 'MandorController::getArea');
    $routes->get('getJobRole', 'MandorController::getJobRole');
    $routes->get('getKaryawan', 'MandorController::getKaryawan');
    $routes->post('penilaianCreate', 'MandorController::create');
    $routes->post('cekPenilaian', 'MandorController::cekPenilaian');
    $routes->post('penilaianStore', 'MandorController::store');
    $routes->get('penilaianDetail/(:num)/(:num)/(:num)', 'PenilaianController::show/$1/$2/$3');
    $routes->get('penilaianExcel/(:num)/(:num)/(:num)', 'PenilaianController::reportExcel/$1/$2/$3');

    $routes->get('reportBatch/(:segment)', 'PenilaianController::reportAreaperBatch/$1');
    $routes->get('reportPenilaian', 'MonitoringController::reportpenilaian');
    $routes->get('reportBatch', 'MonitoringController::reportBatch');
    $routes->get('reportBatch/filterReport', 'MonitoringController::filterReportBatch');
    $routes->get('reportPenilaian/(:segment)', 'MandorController::penilaianPerArea/$1');
    $routes->get('reportPenilaian/(:segment)/(:num)', 'PenilaianController::penilaianPerPeriode/$1/$2');
    $routes->get('reportPenilaian/(:segment)/(:segment)/(:segment)', 'PenilaianController::excelReportPerPeriode/$1/$2/$3');
    $routes->get('reportExcel/(:segment)/(:segment)/(:segment)', 'PenilaianController::reportExcel/$1/$2/$3');
    // http://localhost:8080/Monitoring/exelReportBatch/3/KK1
    $routes->get('exelReportBatch/(:num)/(:segment)', 'PenilaianController::exelReportBatch/$1/$2');

    $routes->get('raportPenilaian/(:any)', 'MandorController::raportPenilaian/$1');

    $routes->get('chat', 'TrainingSchoolController::chat');
    // routes/web.php atau routes.php (tergantung pada versi CodeIgniter)
    $routes->get('contacts', 'ChatController::getContactsWithLastMessage');

    $routes->get('conversation/(:num)/(:num)', 'ChatController::fetchConversation/$1/$2');
    $routes->post('send-message', 'ChatController::sendMessage');
    $routes->get('getContacts/(:num)', 'ChatController::getContacts/$1');
    $routes->post('mark-messages-as-read/(:num)', 'ChatController::markMessagesAsRead/$1');
    $routes->get('count-unread-messages', 'ChatController::countUnreadMessages');
    $routes->get('check-new-messages', 'ChatController::checkNewMessages');
    $routes->get('long-poll-new-messages', 'ChatController::longPollNewMessages'); // Untuk long polling

    $routes->get('evaluasiKaryawan/(:any)/(:any)', 'MandorController::getEmployeeEvaluationStatus/$1/$2');
    $routes->get('instruksiKerja', 'MandorController::instruksiKerja');
});

$routes->group('/TrainingSchool', ['filter' => 'TrainingSchool'], function ($routes) {
    $routes->get('', 'TrainingSchoolController::index');
    $routes->get('dataKaryawan', 'TrainingSchoolController::listArea');
    $routes->get('dataKaryawan/(:any)', 'TrainingSchoolController::detailKaryawanPerArea/$1');
    $routes->get('downloadTemplateKaryawan', 'KaryawanController::downloadTemplate');
    $routes->post('karyawanStoreImport', 'KaryawanController::upload');
    $routes->get('exportKaryawan/(:any)', 'KaryawanController::exportPerArea/$1');

    $routes->get('karyawanCreate', 'KaryawanController::create');
    $routes->post('karyawanStore', 'KaryawanController::store');
    $routes->get('karyawanEdit/(:num)', 'KaryawanController::edit/$1');
    $routes->post('karyawanUpdate/(:num)', 'KaryawanController::update/$1');
    $routes->get('karyawanDelete/(:num)', 'KaryawanController::delete/$1');

    $routes->get('historyPindahKaryawan', 'TrainingSchoolController::historyPindahKaryawan');
    $routes->get('reportHistoryPindahKaryawan', 'HistoryPindahKaryawanController::reportExcel');

    // routes/web.php atau routes.php (tergantung pada versi CodeIgniter)
    $routes->get('contacts', 'ChatController::getContactsWithLastMessage');
    $routes->get('chat', 'TrainingSchoolController::chat');

    // $routes->get('conversation/(:num)/(:num)', 'ChatController::fetchConversation/$1/$2');
    // $routes->post('send-message', 'ChatController::sendMessage');
    $routes->get('conversation/(:num)/(:num)', 'ChatController::fetchConversation/$1/$2');
    $routes->post('send-message', 'ChatController::sendMessage');
    $routes->get('getContacts/(:num)', 'ChatController::getContacts/$1');
    $routes->post('mark-messages-as-read/(:num)', 'ChatController::markMessagesAsRead/$1');
    $routes->get('count-unread-messages', 'ChatController::countUnreadMessages');
    $routes->get('check-new-messages', 'ChatController::checkNewMessages');
    $routes->get('long-poll-new-messages', 'ChatController::longPollNewMessages'); // Untuk long polling


    // penilaian
    $routes->get('reportPenilaian', 'MonitoringController::reportpenilaian');
    $routes->get('reportPenilaian/(:segment)', 'PenilaianController::penilaianPerArea/$1');
    $routes->get('reportPenilaian/(:segment)/(:segment)/(:segment)', 'PenilaianController::excelReportPerPeriode/$1/$2/$3');
    $routes->get('reportBatch', 'MonitoringController::reportBatch');
    $routes->get('reportBatch/(:segment)', 'PenilaianController::reportAreaperBatch/$1');
    $routes->get('exelReportBatch/(:num)/(:segment)', 'PenilaianController::exelReportBatch/$1/$2');
});


$routes->group('api', function ($routes) {
    $routes->get('karyawan', 'Api\KaryawanController::index');
    $routes->get('karyawan/(:segment)', 'Api\KaryawanController::show/$1');
    $routes->post('karyawan', 'Api\KaryawanController::create');
    $routes->put('karyawan/(:segment)', 'Api\KaryawanController::update/$1');
    $routes->delete('karyawan/(:segment)', 'Api\KaryawanController::delete/$1');

    $routes->get('area_utama/(:segment)', 'Api\KaryawanController::getKaryawanByAreaUtama/$1');
    $routes->get('area/(:segment)', 'Api\KaryawanController::getKaryawanByArea/$1');
    $routes->get('getdataforbs/(:any)/(:any)', 'Api\KaryawanController::getDataForBsMc/$1/$2');
});
