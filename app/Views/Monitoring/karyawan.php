<?php $this->extend('Monitoring/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">



    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-header">
                    <h5>
                        Data Karyawan
                    </h5>
                </div>
                <div class="card-body">
                    <a href="<?= base_url('monitoring/') ?>" class="btn btn-primary btn-sm">Tambah Data</a>
                    <!-- import data karyawan -->
                    <a href="<?= base_url('monitoring/karyawanImport') ?>"
                        class="btn btn-success btn-sm import-btn">Import
                        Data Karyawan</a>
                    <!-- kosongkan data karyawan -->
                    <a href="<?= base_url('monitoring/karyawanEmpty') ?>" class="btn btn-danger btn-sm">Kosongkan
                        Data</a>
                    <div class="table-responsive">
                        <table id="karyawanTable" class="table align-items-center mb-0">
                            <thead>
                                <th>No</th>
                                <th>Kode Kartu</th>
                                <th>Nama Karyawan</th>
                                <th>Tgl Masuk</th>
                                <th>Jenis Kelamin</th>
                                <th>Shift</th>
                                <th>Bagian</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                <?php if (!empty($karyawan)) : ?>
                                    <?php foreach ($karyawan as $karyawan) : ?>
                                        <tr>
                                            <td><?= $karyawan['id_karyawan'] ?></td>
                                            <td><?= $karyawan['kode_kartu'] ?></td>
                                            <td><?= $karyawan['nama_karyawan'] ?></td>
                                            <td><?= $karyawan['tgl_masuk'] ?></td>
                                            <td><?= $karyawan['jenis_kelamin'] ?></td>
                                            <td><?= $karyawan['shift'] ?></td>
                                            <td><?= $karyawan['nama_bagian'] ?></td>
                                            <td><?= $karyawan['status'] ?></td>
                                            <td>
                                                <a class="btn btn-warning btn-sm">Edit</a>
                                                <a class="btn btn-danger btn-sm">Hapus</a>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Data tidak ditemukan</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Initialize DataTable with export options
        $('#karyawanTable').DataTable({});

        // Flash message SweetAlerts
        <?php if (session()->getFlashdata('success')) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= session()->getFlashdata('success') ?>',
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '<?= session()->getFlashdata('error') ?>',
            });
        <?php endif; ?>
    });
</script>


<?php $this->endSection(); ?>