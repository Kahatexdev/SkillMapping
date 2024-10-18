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
                        Form Edit Data Job Role
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('monitoring/jobroleUpdate/' . $jobrole['id_jobrole']) ?>" method="post">
                        <div class="form-group mb-2">
                            <label for="id_bagian">ID Bagian</label>
                            <select name="id_bagian" id="id_bagian" class="form-control" required>
                                <option value="">Pilih Bagian</option>
                                <?php foreach ($bagians as $bagian) : ?>
                                <option value="<?= $bagian['id_bagian'] ?>"
                                    <?= $jobrole['id_bagian'] == $bagian['id_bagian'] ? 'selected' : '' ?>>
                                    <?= $bagian['id_bagian'] ?> -
                                    <?= $bagian['nama_bagian'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="">Pilih Status</option>
                                <option value="aktif" <?= $jobrole['status'] == 'aktif' ? 'selected' : '' ?>>Aktif
                                </option>
                                <option value="nonaktif" <?= $jobrole['status'] == 'nonaktif' ? 'selected' : '' ?>>
                                    Nonaktif</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="jobdesc">Jobdesk</label>
                            <input type="text" class="form-control" name="jobdesc" id="jobdesc"
                                value="<?= $jobrole['jobdesc'] ?>" required>
                        </div>
                        <input type="hidden" name="id_jobrole" value="<?= $jobrole['id_jobrole'] ?>">
                        <a href="<?= base_url('monitoring/jobrole') ?>" class="btn btn-secondary btn-sm">Kembali</a>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>