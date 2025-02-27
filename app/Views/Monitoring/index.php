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
</div>
<div class="container py-4">
    <!-- Header Monitoring -->
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="card-title h4 mb-0">Monitoring Penilaian Karyawan</h2>
        </div>
    </div>

    <!-- Card Monitoring -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if (!empty($cekPenilaian)) : ?>
            <?php foreach ($cekPenilaian as $mandor) : ?>
                <?php
                if ($mandor['total_karyawan'] == 0) {
                    continue;
                }
                // Hitung persentase penilaian
                $progress = round(($mandor['total_penilaian'] / $mandor['total_karyawan']) * 100);
                $isComplete = $progress >= 100;
                ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title"><?= esc($mandor['username']); ?></h5>
                                <!-- Tombol modal -->
                                <button type="button" class="btn btn-sm <?= $isComplete ? 'btn-success' : 'btn-danger' ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalEmployeeEvaluation" data-area="<?= esc($mandor['username']); ?>">
                                    <?= $isComplete ? 'Selesai' : 'Belum Selesai'; ?>
                                </button>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Karyawan: <?= esc($mandor['total_karyawan']); ?></small>
                                <small class="text-muted">Dinilai: <?= esc($mandor['total_penilaian']); ?></small>
                            </div>
                            <div class="progress mb-3" style="height: 20px;">
                                <div class="progress-bar <?= $isComplete ? 'bg-success' : 'bg-info' ?>"
                                    role="progressbar"
                                    style="width: <?= $progress; ?>%; height: 20px;"
                                    aria-valuenow="<?= $progress; ?>"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                    <?= $progress; ?>%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal untuk menampilkan data karyawan yang belum dinilai -->
                <div class="modal fade" id="modalEmployeeEvaluation" tabindex="-1" aria-labelledby="modalEmployeeEvaluationLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalEmployeeEvaluationLabel">Karyawan Belum Dinilai</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered w-100">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Kode Kartu</th>
                                                <th>Nama Karyawan</th>
                                                <th>Shift</th>
                                                <th>Bagian</th>
                                                <th>Area</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="employeeEvaluationBody">
                                            <!-- Data akan dimuat secara dinamis -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-12">
                <div class="alert alert-warning" role="alert">
                    Tidak ada data yang tersedia.
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>
<!-- 

</div> -->

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
    document.addEventListener("DOMContentLoaded", function() {
        // Ambil referensi modal
        var modalElement = document.getElementById('modalEmployeeEvaluation');

        // Saat modal mulai ditampilkan
        modalElement.addEventListener('show.bs.modal', function(event) {
            // Tentukan id_periode dan area; sesuaikan nilainya dengan konteks Anda
            var id_periode = '1'; // misal
            var area = event.relatedTarget.getAttribute('data-area');

            // Panggil endpoint untuk mendapatkan data evaluasi karyawan
            fetch("<?= base_url('Monitoring/evaluasiKaryawan') ?>/" + id_periode + "/" + area)
                .then(response => response.json())
                .then(data => {
                    // Filter hanya data dengan status "Belum Dinilai"
                    var belumDinilai = data.filter(emp => emp.status === "Belum Dinilai");
                    var tbody = document.getElementById("employeeEvaluationBody");
                    tbody.innerHTML = ""; // kosongkan isi tabel

                    if (belumDinilai.length > 0) {
                        belumDinilai.forEach(function(emp) {
                            var tr = document.createElement("tr");
                            tr.innerHTML = 
                            // nomor urut
                            "<td>" + (tbody.rows.length + 1) + "</td>" +
                                "<td>" + emp.kode_kartu + "</td>" +
                                "<td>" + emp.nama_karyawan + "</td>" +
                                "<td>" + emp.shift + "</td>" +
                                "<td>" + emp.nama_bagian + "</td>" +
                                "<td>" + emp.area + "</td>" +
                                "<td><span class='badge bg-danger'>Belum Dinilai</span></td>";
                            tbody.appendChild(tr);
                        });
                    } else {
                        tbody.innerHTML = "<tr><td colspan='7'>Karyawan Sudah Dinilai Semua.</td></tr>";
                    }
                })
                .catch(error => {
                    console.error("Error fetching data:", error);
                });
        });
    });
</script>
<?php $this->endSection(); ?>