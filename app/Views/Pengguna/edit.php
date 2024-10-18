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
                        Form Edit Data User
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('monitoring/userUpdate/' . $user['id_user']) ?>" method="post">
                        <div class="form-group mb-2">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" name="username" id="username"
                                value="<?= $user['username'] ?>" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password" id="password"
                                value="<?= $user['password'] ?>" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="role">Role</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="">Pilih Role</option>
                                <option value="mandor" <?= $user['role'] == 'mandor' ? 'selected' : '' ?>>mandor
                                </option>
                                <option value="monitoring" <?= $user['role'] == 'monitoring' ? 'selected' : '' ?>>
                                    Monitoring</option>
                            </select>
                        </div>
                        <a href="<?= base_url('monitoring/dataUser') ?>" class="btn btn-secondary btn-sm">Kembali</a>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>