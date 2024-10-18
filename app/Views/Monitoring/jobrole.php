<?php $this->extend('Monitoring/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('success')) : ?>
    <script>
    $(document).ready(function() {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?= session()->getFlashdata('success') ?>',
        });
    });
    </script>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
    <script>
    $(document).ready(function() {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?= session()->getFlashdata('error') ?>',
        });
    });
    </script>
    <?php endif; ?>



    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-header">
                    <h5>
                        Data Job Role
                    </h5>
                </div>
                <div class="card-body">
                    <a href="<?= base_url('monitoring/jobroleCreate') ?>" class="btn btn-primary btn-sm">Tambah Job</a>
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <th>No</th>
                                <th>ID Bagian</th>
                                <th>Status</th>
                                <th>Jobdesk</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                <?php foreach ($jobrole as $jobrole) : ?>
                                <tr>
                                    <td><?= $jobrole['id_jobrole'] ?></td>
                                    <td><?= $jobrole['id_bagian']?> - <?= $jobrole['nama_bagian'] ?></td>
                                    <td><?= $jobrole['status'] ?></td>
                                    <td><?= $jobrole['jobdesc'] ?></td>
                                    <td>
                                        <a href="<?= base_url('monitoring/jobroleEdit/' . $jobrole['id_jobrole']) ?>"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <a href="<?= base_url('monitoring/jobroleDelete/' . $jobrole['id_jobrole']) ?>"
                                            class="btn btn-danger btn-sm">Delete</a>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>