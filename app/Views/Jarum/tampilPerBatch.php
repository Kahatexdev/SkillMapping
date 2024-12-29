<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Human Resource System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Data Pemakaian Jarum
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-3 col-sm-3 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url('Monitoring/summaryJarum') ?>">
                <div class="card">
                    <div class="card-body w-100">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold"></p>
                                    <h5 class="font-weight-bolder mb-0">Batch 1 2024
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-sm-3 mb-xl-0 mb-4 mt-2">
            <a href="">
                <div class="card">
                    <div class="card-body w-100">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold"></p>
                                    <h5 class="font-weight-bolder mb-0">Batch 2 2024
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-sm-3 mb-xl-0 mb-4 mt-2">
            <a href="">
                <div class="card">
                    <div class="card-body w-100">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold"></p>
                                    <h5 class="font-weight-bolder mb-0">Batch 3 2024
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-sm-3 mb-xl-0 mb-4 mt-2">
            <a href="">
                <div class="card">
                    <div class="card-body w-100">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold"></p>
                                    <h5 class="font-weight-bolder mb-0">Batch 4 2024
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

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





<?php $this->endSection(); ?>