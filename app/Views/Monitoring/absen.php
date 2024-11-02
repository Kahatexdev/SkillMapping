<?php $this->extend('Monitoring/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">



    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-header">
                    <h5>
                        Data Absen
                    </h5>
                </div>
                <div class="card-body">
                    <a href="<?= base_url('monitoring/absenCreate') ?>" class="btn btn-primary btn-sm">Tambah Absen</a>
                    <a href="<?= base_url('monitoring/absenImport') ?>" class="btn btn-success btn-sm import-btn">Import
                        Absen</a>
                    <!-- kosongkan data absen -->
                    <a href="<?= base_url('monitoring/absenEmpty') ?>" class="btn btn-danger btn-sm">Kosongkan Data
                        Absen</a>
                    <div class="table-responsive">
                        <table id="absenTable" class="table align-items-center mb-0">
                            <thead>
                                <th>No</th>
                                <th>ID Karyawan</th>
                                <th>Tanggal</th>
                                <th>Keterangan Absen</th>
                                <th>ID User</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>

                                <?php if (!empty($absen)) : ?>
                                <?php foreach ($absen as $absen) : ?>
                                <tr>
                                    <td><?= $absen['id_absen'] ?></td>
                                    <td><?= $absen['nama_karyawan'] ?></td>
                                    <td><?= $absen['tanggal'] ?></td>
                                    <td><?= $absen['ket_absen'] ?></td>
                                    <td><?= $absen['id_user'] ?></td>
                                    <td>
                                        <a href="<?= base_url('monitoring/absenEdit/' . $absen['id_absen']) ?>"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="confirmDelete('<?= $absen['id_absen'] ?>')">Delete</button>
                                </tr>
                                <?php endforeach; ?>
                                <?php else : ?>
                                <tr>
                                    <td colspan="5" class="text-center">Data tidak ditemukan</td>
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

<script type="text/javascript">
function confirmDelete(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= base_url('monitoring/absenDelete/') ?>" + id;
        }
    })
}
</script>
<script>
$(document).ready(function() {
    // Initialize DataTable with export options
    $('#absenTable').DataTable({});

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