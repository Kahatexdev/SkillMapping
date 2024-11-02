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
                        Import Absen
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('monitoring/absenStoreImport') ?>" method="post"
                        enctype="multipart/form-data">
                        <div class="form-group mb-2">
                            <label for="file">File Excel</label>
                            <input type="file" class="form-control" name="file" id="file" required>

                            <small class="text-danger">*File harus berformat .xls atau .xlsx</small>
                            <small class="text-danger">*Pastikan file excel sesuai dengan format yang telah
                                ditentukan</small>
                            <small class="text-danger">*Pastikan file excel tidak kosong</small>
                        </div>
                        <!-- download template karyawan dari controller -->
                        <a href="<?= base_url('monitoring/downloadTemplateAbsen') ?>"
                            class="btn btn-success btn-sm mt-2" target="_blank">Download
                            Template</a>
                        <a href="<?= base_url('monitoring/dataAbsen') ?>"
                            class="btn btn-secondary btn-sm mt-2">Kembali</a>
                        <button type="submit" class="btn btn-primary btn-sm mt-2">Import</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>