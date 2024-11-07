<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">

    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="float-start">
                        Data Karyawan
                    </h5>
                    <div class="col text-end">
                        <a href="<?= base_url('mandor/downloadTemplateKaryawan') ?>"
                            class="btn bg-gradient-success btn-sm">Download Template Excel</a>
                        <a href="<?= base_url('mandor/karyawanCreate') ?>" class="btn bg-gradient-info btn-sm">Input
                            Data Karyawan</a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- form import  data karyawan -->
                    <form action="<?= base_url('mandor/karyawanStoreImport') ?>" method="post"
                        enctype="multipart/form-data">
                        <div class="form-group mb-2">
                            <label for="file">File Excel</label>
                            <div class="file-upload-wrapper">
                                <label class="file-upload-button" id="file-label" for="file">
                                    <i class="fas fa-upload"></i> Pilih File
                                </label>
                                <input type="file" class="file-upload-input" name="file" id="file" required>
                            </div>
                            <small class="text-danger" style="font-size: smaller;">*File harus berformat .xls atau
                                .xlsx</small>
                            <small class="text-danger" style="font-size: smaller;">*Pastikan file excel sesuai dengan
                                format yang telah
                                ditentukan</small>
                            <small class="text-danger" style="font-size: smaller;">*Pastikan file excel tidak
                                kosong</small>
                        </div>
                        <button type="submit" class="btn bg-gradient-info btn-lg w-100">Import</button>
                    </form>

                    <div class="table-responsive">
                        <table id="karyawanTable" class="table table-striped table-hover table-bordered">
                            <thead>
                                <th>No</th>
                                <th>Kode Kartu</th>
                                <th>Nama Karyawan</th>
                                <th>Shift</th>
                                <th>Jenis Kelamin</th>
                                <th>Libur</th>
                                <th>Libur Tambahan</th>
                                <th>Warna Baju</th>
                                <th>Status Baju</th>
                                <th>Tanggal Lahir</th>
                                <th>Tanggal Masuk</th>
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
                                    <td><?= $karyawan['shift'] ?></td>
                                    <td><?= $karyawan['jenis_kelamin'] ?></td>
                                    <td><?= $karyawan['libur'] ?></td>
                                    <td><?= $karyawan['libur_tambahan'] ?></td>
                                    <td><?= $karyawan['warna_baju'] ?></td>
                                    <td><?= $karyawan['status_baju'] ?></td>
                                    <td><?= $karyawan['tgl_lahir'] ?></td>
                                    <td><?= $karyawan['tgl_masuk'] ?></td>
                                    <td><?= $karyawan['nama_bagian'] . ' - ' . $karyawan['area'] ?></td>
                                    <td><?= $karyawan['status_aktif'] ?></td>
                                    <td>
                                        <a class="btn btn-warning btn-sm"
                                            href="<?= base_url('mandor/karyawanEdit/' . $karyawan['id_karyawan']) ?>">Edit</a>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="confirmDelete('<?= $karyawan['id_karyawan'] ?>')">Delete</button>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                                <?php else : ?>
                                <tr>
                                    <td colspan="14" class="text-center">No users found</td>
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
            window.location.href = "<?= base_url('mandor/karyawanDelete/') ?>" + id;
        }
    })
}
</script>
<script>
$(document).ready(function() {
    // Initialize DataTable with export options
    $('#karyawanTable').DataTable({});

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
<script>
$(document).ready(function() {
    // File input
    $('.file-upload-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $('#file-label').html('<i class="fas fa-upload"></i> ' + fileName);
    });


});
</script>

<?php $this->endSection(); ?>