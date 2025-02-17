<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>

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
                                    <h5 class="font-weight-bolder mb-0"><?= $TtlKaryawan ?></h5>
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
            <a href="#">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Perpindahan Bulan Ini</p>
                                    <h5 class="font-weight-bolder mb-0"><?= $PerpindahanBulanIni ?></h5>
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
    <!-- <div class="row mt-4">
        <div class="col-xl-6 col-lg-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="mb-0">Distribusi Skill</h6>
                </div>
                <div class="card-body">
                    <canvas id="skillChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="mb-0">Tren Performa Karyawan</h6>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>
    </div> -->
</div>

<!-- Library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Inisialisasi Chart Distribusi Skill
    var ctxSkill = document.getElementById('skillChart').getContext('2d');
    var skillChart = new Chart(ctxSkill, {
        type: 'pie',
        data: {
            labels: ['Skill A', 'Skill B', 'Skill C'],
            datasets: [{
                data: [30, 40, 30], // Ganti dengan data dinamis
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });

    // Inisialisasi Chart Tren Performa Karyawan
    var ctxPerformance = document.getElementById('performanceChart').getContext('2d');
    var performanceChart = new Chart(ctxPerformance, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'], // Contoh label waktu
            datasets: [{
                label: 'Performa',
                data: [65, 59, 80, 81, 56], // Ganti dengan data dinamis
                fill: false,
                borderColor: '#4BC0C0',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php $this->endSection(); ?>