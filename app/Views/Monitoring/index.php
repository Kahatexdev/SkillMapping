<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="row mb-5">
        <!-- Card 1: Jumlah Karyawan -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Jumlah Karyawan</h5>
                    <h1>100</h1>
                </div>
            </div>
        </div>
        <!-- Card 2: Jumlah Absen -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Jumlah Absen</h5>
                    <h1>100</h1>
                </div>
            </div>
        </div>
        <!-- Card 3: Jumlah Job Role -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Jumlah Job Role</h5>
                    <h1>100</h1>
                </div>
            </div>
        </div>
        <!-- Card 4: Jumlah Penilaian -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Jumlah Penilaian</h5>
                    <h1>100</h1>
                </div>
            </div>
        </div>
    </div>


    <!-- Row for Charts -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Penilaian Bulanan</h5>
                </div>
                <div class="card-body">
                    <canvas id="penilaianChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Distribusi Grade</h5>
                </div>
                <div class="card-body">
                    <canvas id="gradeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Penilaian Bulanan Chart
    var ctx1 = document.getElementById('penilaianChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'], // Ganti dengan data dinamis
            datasets: [{
                label: 'Penilaian',
                data: [10, 20, 30, 40, 50, 60], // Ganti dengan data dinamis
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
            }]
        }
    });

    // Distribusi Grade Chart
    var ctx2 = document.getElementById('gradeChart').getContext('2d');
    new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: ['A', 'B', 'C', 'D'], // Ganti dengan data dinamis
            datasets: [{
                data: [30, 50, 10, 10], // Ganti dengan data dinamis
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545']
            }]
        }
    });
</script>

<?php $this->endSection(); ?>