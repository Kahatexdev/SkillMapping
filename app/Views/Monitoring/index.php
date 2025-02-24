<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>

<style>
    canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }

    .card-header {
        background-color: #5e72e4;
    }
</style>
<div class="container-fluid py-4">
    <!-- Statistik Cards -->
    <div class="row">
        <!-- Card Total Karyawan -->
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="#">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Karyawan</p>
                                    <h5 class="font-weight-bolder mb-0"><?= $TtlKaryawan ?> Orang</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="fas fa-users text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card Perpindahan Bulan Ini -->
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url($role . '/historyPindahKaryawan'); ?>">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Perpindahan Bulan Ini</p>
                                    <h5 class="font-weight-bolder mb-0"><?= $PerpindahanBulanIni ?> Orang</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                    <i class="fas fa-exchange-alt text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card Rata-rata Grade -->
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="#">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Rata-rata Grade</p>
                                    <h5 class="font-weight-bolder mb-0"><?= $RataRataGrade ?></h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                    <i class="fas fa-star text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card Skill Gap -->
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="#">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Skill Gap</p>
                                    <h5 class="font-weight-bolder mb-0"><?= $SkillGap ?></h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                                    <i class="fas fa-chart-line text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Grafik Data -->
    <div class="row mt-4">
        <!-- Diagram Batang: Total Karyawan Berdasarkan Bagian -->
        <div class="col-5 mb-4">
            <div class="card">
                <div class="card-body">
                    <canvas id="karyawanBarChart" style="width: 100%; height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Diagram Batang: Grafik Pindahan Karyawan -->
        <div class="col-7 mb-4">
            <div class="card">
                <div class="card-body">
                    <canvas id="pindahanBarChart" style="width: 100%; height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Header Monitoring -->
            <div class="card card-frame mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bolder">Monitoring Penilaian Karyawan</h5>
                    </div>
                </div>
            </div>
            <!-- Tabel Monitoring -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="cekPenilaianTable" class="table table-striped table-hover table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Area</th>
                                    <th>Jumlah Karyawan</th>
                                    <th>Sudah Dinilai</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($cekPenilaian)) : ?>
                                    <?php foreach ($cekPenilaian as $mandor) : ?>
                                        <?php if ($mandor['total_karyawan'] == 0) {
                                            continue;
                                        } ?>
                                        <tr>
                                            <td><?= esc($mandor['username']); ?></td>
                                            <td><?= esc($mandor['total_karyawan']); ?></td>
                                            <td><?= esc($mandor['total_penilaian']); ?></td>
                                            <td>
                                                <?php if ($mandor['total_penilaian'] >= $mandor['total_karyawan']) : ?>
                                                    <span class="badge bg-info">Selesai</span>
                                                <?php else : ?>
                                                    <span class="badge bg-danger">Belum Selesai</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="4">Tidak ada data</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Library Chart.js -->
<!-- Library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Contoh data dari PHP (sesuaikan dengan data Anda)
    <?php
    $labels = [];
    $values = [];
    foreach ($karyawanByBagian as $row) {
        if ($row['nama_bagian'] == '-') {
            continue;
        }
        $labels[] = $row['nama_bagian'];
        $values[] = (int)$row['jumlah_karyawan'];
    }
    ?>

    // Chart Batang untuk Total Karyawan Berdasarkan Bagian
    // Chart Batang untuk Total Karyawan Berdasarkan Bagian
    var ctxBar = document.getElementById('karyawanBarChart').getContext('2d');
    var karyawanBarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Total Karyawan',
                data: <?= json_encode($values) ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Bagian'
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Karyawan'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Total Karyawan Berdasarkan Bagian'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Jumlah: ' + context.parsed.y;
                        }
                    }
                },
                legend: {
                    display: true
                }
            }
        }
    });
</script>

<script>
    // Chart Batang untuk Grafik Pindahan Karyawan
    var ctxBar2 = document.getElementById('pindahanBarChart').getContext('2d');
    var pindahanBarChart = new Chart(ctxBar2, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelsKar) ?>,
            datasets: [{
                label: 'Jumlah Pindahan',
                data: <?= json_encode($valuesKar) ?>,
                backgroundColor: 'rgba(255, 159, 64, 0.6)', // Sesuaikan warna
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1,
                // Pengaturan lebar batang
                barPercentage: 0.6,
                categoryPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            // indexAxis: 'y', // Jika ingin horizontal bar, aktifkan ini
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Tanggal'
                    },
                    // Jika label tanggal terlalu panjang, silakan atur rotasi:
                    // ticks: {
                    //     maxRotation: 45,
                    //     minRotation: 0
                    // }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Pindahan'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Grafik Pindahan Karyawan'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Jumlah: ' + context.parsed.y;
                        }
                    }
                },
                legend: {
                    display: true
                }
            }
        }
    });
</script>

<script>
    $(document).ready(function() {
        $('#cekPenilaianTable').DataTable({});
    });
</script>
<?php $this->endSection(); ?>