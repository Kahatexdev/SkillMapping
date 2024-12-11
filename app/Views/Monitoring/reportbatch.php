<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            Skill Mapping
                            <h4 class="font-weight-bolder">
                                Report Batch Penilaian
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href="" class="btn bg-gradient-info">
                                    <i class="fas fa-user-tie text-lg opacity-10" aria-hidden="true"></i>
                                </a>
                                <div> &nbsp;</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-4">
        <?php foreach ($getBatch as $b) : ?>
            <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title text-uppercase text-muted mb-0"><?= $b['nama_batch'] ?></h5>
                                <span class="h2 font-weight-bold mb-0">Area <?= $b['area_utama'] ?></span>
                                <a href="<?= base_url('Monitoring/exelReportBatch/' . $b['id_batch'] . '/' . $b['area_utama']) ?>" class="btn btn-outline-success mt-2">
                                    <i class="fas fa-file-excel text-lg me-2"></i>Download Report
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>
<!-- datatable -->
<script>
    $(document).ready(function() {
        // Initialize DataTable with export options
        $('#table_report_batch').DataTable({});

        // Flash message SweetAlerts
        <?php if (session()->getFlashdata('success')) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                html: '<?= session()->getFlashdata('success') ?>',
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                html: '<?= session()->getFlashdata('error') ?>',
            });
        <?php endif; ?>
    });
</script>

<style>
    .card-body {
        padding: 1.5rem 1.5rem 0.5rem 1.5rem !important;
    }
    
    .card-title {
        font-size: 1.2rem;
    }

    .card-stats {
        border-radius: 1rem;
        border: 1px solid #e9ecef;
    }

    .card-stats:hover {
        box-shadow: 0 0 11px rgba(33, 33, 33, 0.2);
    }
</style>
<?php $this->endSection(); ?>