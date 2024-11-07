<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>',
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
                    html: '<?= session()->getFlashdata('error') ?>',
                });
            });
        </script>
    <?php endif; ?>

    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-header">
                    <h5>
                        Import Karyawan
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('monitoring/karyawanStoreImport') ?>" method="post"
                        enctype="multipart/form-data">
                        <div class="form-group mb-2">
                            <label for="file">File Excel</label>
                            <div class="file-upload-wrapper">
                                <label class="file-upload-button" id="file-label" for="file">
                                    <i class="fas fa-upload"></i> Pilih File
                                </label>
                                <input type="file" class="file-upload-input" name="file" id="file" required>
                            </div>
                            <small class="text-danger">*File harus berformat .xls atau .xlsx</small>
                            <small class="text-danger">*Pastikan file excel sesuai dengan format yang telah
                                ditentukan</small>
                            <small class="text-danger">*Pastikan file excel tidak kosong</small>
                        </div>
                        <!-- download template karyawan dari controller -->
                        <a href="<?= base_url('monitoring/downloadTemplateKaryawan') ?>"
                            class="btn btn-success btn-sm mt-2" target="_blank">Download Template Excel</a>
                        <a href="<?= base_url('monitoring/datakaryawan') ?>"
                            class="btn btn-secondary btn-sm mt-2">Kembali</a>
                        <button type="submit" class="btn btn-primary btn-sm mt-2">Import</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('file').addEventListener('change', function() {
        var fileLabel = document.getElementById('file-label');
        var uploadIcon = document.getElementById('upload-icon');

        // Menghapus ikon upload dan menampilkan nama file
        uploadIcon.style.display = 'none';
        fileLabel.innerHTML = this.files[0].name;
        console.log(this.files[0].name);
    });
</script>
<?php $this->endSection(); ?>

<!-- Include Font Awesome for the upload icon -->
<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> -->