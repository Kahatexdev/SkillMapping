<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <!-- Row for Cards -->
    <div class="row mb-5">
        <!-- Card for Monthly Evaluation -->
        <div class="col-xl-6 col-md-6 mb-xl-0 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6>Penilaian Bulanan</h6>
                </div>
                <div class="card-body p-3">
                    <canvas id="penilaianChart"></canvas>
                </div>
            </div>
        </div>
        <!-- Card for Grade Distribution -->
        <div class="col-xl-6 col-md-6 mb-xl-0 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6>Distribusi Grade</h6>
                </div>
                <div class="card-body p-3">
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
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Penilaian',
                data: <?= json_encode($monthlyEvaluations) ?>, // Data dinamis
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4, // Curved line effect
                pointBackgroundColor: '#ffffff',
                pointRadius: 5,
                pointHoverRadius: 7

            }]
        }
    });

    // Distribusi Grade Chart
    var ctx2 = document.getElementById('gradeChart').getContext('2d');
    new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: ['A', 'B', 'C', 'D'],
            datasets: [{
                data: <?= json_encode($gradeDistribution) ?>, // Data dinamis
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545'],
                borderWidth: 1
            }]
        }
    });
</script>

<?php $this->endSection(); ?>