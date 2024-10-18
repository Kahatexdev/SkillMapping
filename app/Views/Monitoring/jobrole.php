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
                                <?php if (!empty($jobrole)) : ?>
                                <?php foreach ($jobrole as $jobrole) : ?>
                                <tr>
                                    <td><?= $jobrole['id_jobrole'] ?></td>
                                    <td><?= $jobrole['id_bagian']?> - <?= $jobrole['nama_bagian'] ?></td>
                                    <td><?= $jobrole['status'] ?></td>
                                    <td><?= $jobrole['jobdesc'] ?></td>
                                    <td>
                                        <a href="<?= base_url('monitoring/jobroleEdit/' . $jobrole['id_jobrole']) ?>"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="confirmDelete('<?= $jobrole['id_jobrole'] ?>')">Delete</button>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                                <?php else : ?>
                                <tr>
                                    <td colspan="5" class="text-center">No users found</td>
                                </tr>
                                <?php endif ?>
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
            window.location.href = "<?= base_url('monitoring/jobroleDelete//') ?>" + id;
        }
    })
}
</script>
<?php $this->endSection(); ?>