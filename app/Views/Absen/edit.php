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
                        Form Edit Data Absen
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('monitoring/absenUpdate/' . $data['id_absen']) ?>" method="post">

                        <div class="form-group mb-2">
                            <label for="id_karyawan">ID Karyawan</label>
                            <select name="id_karyawan" id="id_karyawan" class="form-control" required>
                                <option value="">Pilih Karyawan</option>
                                <?php foreach ($datajoin as $karyawan) : ?>
                                <option value="<?= $karyawan['id_karyawan'] ?>"
                                    <?= $data['id_karyawan'] == $karyawan['id_karyawan'] ? 'selected' : '' ?>>
                                    <?= $karyawan['id_karyawan'] ?> - <?= $karyawan['nama_karyawan'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control"
                                value="<?= $data['tanggal'] ?>" required>
                        </div>

                        <div class="form-group mb-2">
                            <label for="ket_absen">Keterangan Absen</label>
                            <select name="ket_absen" id="ket_absen" class="form-control" required>
                                <option value="">Pilih Keterangan Absen</option>
                                <option value="Hadir" <?= $data['ket_absen'] == 'Hadir' ? 'selected' : '' ?>>Hadir
                                </option>
                                <option value="Izin" <?= $data['ket_absen'] == 'Izin' ? 'selected' : '' ?>>Izin</option>
                                <option value="Sakit" <?= $data['ket_absen'] == 'Sakit' ? 'selected' : '' ?>>Sakit
                                </option>
                                <option value="Cuti" <?= $data['ket_absen'] == 'Cuti' ? 'selected' : '' ?>>Cuti</option>
                                <option value="Alpa" <?= $data['ket_absen'] == 'Alpa' ? 'selected' : '' ?>>Alpa</option>
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            <label for="id_user">ID User</label>
                            <select name="id_user" id="id_user" class="form-control" required>
                                <option value="">Pilih User</option>
                                <?php foreach ($users as $user) : ?>
                                <option value="<?= $user['id_user'] ?>"
                                    <?= $data['id_user'] == $user['id_user'] ? 'selected' : '' ?>>
                                    <?= $user['id_user'] ?> - <?= $user['username'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <a href="<?= base_url('monitoring/absen') ?>" class="btn btn-secondary btn-sm">Kembali</a>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>