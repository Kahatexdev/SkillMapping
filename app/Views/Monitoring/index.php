<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h6>Total Karyawan</h6>
                    <h3><?= $total_karyawan ?></h3>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h6>Perpindahan Bulan Ini</h6>
                    <h3><?= $total_perpindahan_bulan ?></h3>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h6>Rata-rata Grade</h6>
                    <h3><?= $rata_rata_grade ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Section for Charts and Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#chart-tab" role="tab">Grafik</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#table-tab" role="tab">Tabel</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Chart Tab -->
                        <div class="tab-pane fade show active" id="chart-tab" role="tabpanel">
                            <div class="row">
                                <!-- Fluktuasi Grade Chart -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6>Fluktuasi Grade</h6>
                                            <canvas id="gradeChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Perpindahan Area Bar Chart -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6>Statistik Perpindahan</h6>
                                            <canvas id="areaChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table Tab -->
                        <div class="tab-pane fade" id="table-tab" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Area Lama</th>
                                            <th>Area Baru</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($karyawan as $index => $data): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= $data['nama'] ?></td>
                                                <td><?= $data['area_lama'] ?></td>
                                                <td><?= $data['area_baru'] ?></td>
                                                <td><?= $data['tanggal'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data for Fluktuasi Grade Chart
    const gradeChartCtx = document.getElementById('gradeChart').getContext('2d');
    const gradeChart = new Chart(gradeChartCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Grade Fluctuation',
                data: <?= json_encode($grades) ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2,
            }],
        },
        options: {
            responsive: true
        },
    });

    // Data for Perpindahan Area Bar Chart
    const areaChartCtx = document.getElementById('areaChart').getContext('2d');
    const areaChart = new Chart(areaChartCtx, {
        type: 'bar',
        data: {
            // labels: json_encode($id_bagian_asal) ?>,
            labels: ['Area 1', 'Area 2', 'Area 3', 'Area 4', 'Area 5'],
            datasets: [{
                label: 'Perpindahan',
                // data: json_encode($id_bagian_baru) ?>,
                data: [5, 10, 15, 20, 25],
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
            }],
        },
        options: {
            responsive: true
        },
    });
</script>

<?php $this->endSection(); ?>