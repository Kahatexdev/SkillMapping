<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="float-start">
                        Data Penilaian
                    </h5>
                    <div class="col text-end">
                        <a href="<?= base_url('mandor/downloadTemplatePenilaian') ?>"
                            class="btn bg-gradient-success btn-sm">Download Template Excel</a>
                        <a href="<?= base_url('mandor/penilaianCreate') ?>" class="btn bg-gradient-info btn-sm">Input
                            Data Penilaian</a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Form Import  Data Penilaian -->
                    <form action="<?= base_url('mandor/penilaianStoreImport') ?>" method="post"
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
                        <table id="penilaianTable" class="table table-striped table-hover table-bordered">
                            <thead>
                                <th>No</th>
                                <th>ID Penilaian</th>
                                <th>ID Batch</th>
                                <th>Bobot Nilai</th>
                                <th>Index Nilai</th>
                                <th>Tanggal Penilaian</th>
                                <th>Keterangan</th>
                                <th>ID User</th>
                                <th>ID Job Role</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                <?php if (!empty($penilaian)) : ?>
                                <?php foreach ($penilaian as $penilaian) : ?>
                                <tr>
                                    <td><?= $penilaian['id_penilaian'] ?></td>
                                    <td><?= $penilaian['karyawan_id'] ?></td>
                                    <td><?= $penilaian['id_batch'] ?></td>
                                    <td><?= $penilaian['bobot_nilai'] ?></td>
                                    <td><?= $penilaian['index_nilai'] ?></td>
                                    <td><?= $penilaian['tanggal_penilaian'] ?></td>
                                    <td><?= $penilaian['keterangan'] ?></td>
                                    <td><?= $penilaian['id_user'] ?></td>
                                    <td><?= $penilaian['id_jobrole'] ?></td>
                                    <td>
                                        <a class="btn btn-warning btn-sm"
                                            href="<?= base_url('mandor/penilaianEdit/' . $penilaian['id_penilaian']) ?>">Edit</a>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="confirmDelete('<?= $penilaian['id_penilaian'] ?>')">Delete</button>
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
            window.location.href = "<?= base_url('mandor/penilaianDelete/') ?>" + id;
        }
    })
}
</script>
<script>
$(document).ready(function() {
    // Initialize DataTable with export options
    $('#penilaianTable').DataTable({});

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