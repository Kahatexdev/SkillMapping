<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">

    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-header">
                    <h5>Data User</h5>
                </div>
                <div class="card-body">
                    <a href="<?= base_url('monitoring/userCreate') ?>" class="btn btn-primary btn-sm">Tambah User</a>
                    <div class="table-responsive">
                        <table id="userTable" class="table table-striped table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Role</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)) : ?>
                                <?php foreach ($users as $user) : ?>
                                <tr>
                                    <td><?= $user['id_user'] ?></td>
                                    <td><?= $user['username'] ?></td>
                                    <td><?= $user['password'] ?></td>
                                    <td><?= $user['role'] ?></td>
                                    <td>
                                        <a href="<?= base_url('monitoring/userEdit/' . $user['id_user']) ?>"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="confirmDelete('<?= $user['id_user'] ?>')">Delete</button>
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
            window.location.href = "<?= base_url('monitoring/userDelete/') ?>" + id;
        }
    })
}
</script>
<script>
$(document).ready(function() {
    // Initialize DataTable with export options
    $('#userTable').DataTable({});

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