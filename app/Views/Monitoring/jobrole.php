<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">

    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="font-weight-bolder mb-0">
                                <a href="" # class="btn bg-gradient-info">
                                    <!-- icon data karyawan -->
                                    <i class="fas fa-user-tie text-lg opacity-10" aria-hidden="true"></i>
                                </a>
                                Data Job Role
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('monitoring/jobroleCreate') ?>" class="btn bg-gradient-info btn-sm">
                                    <i class="fas fa-plus text-lg opacity-10" aria-hidden="true"></i>
                                    Job Role
                                </a>
                                <div> &nbsp;</div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        Tabel Data Job Role
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="jobroleTable" class="table table-striped table-hover table-bordered">
                            <thead>
                                <th>No</th>
                                <th>Nama Bagian</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Jobdesk</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                <?php if (!empty($jobrole)) : ?>
                                    <?php foreach ($jobrole as $jobrole) : ?>
                                        <tr>
                                            <td><?= $jobrole['id_jobrole'] ?></td>
                                            <td><?= $jobrole['nama_bagian'] ?> - <?= $jobrole['area'] ?></td>
                                            <td><?= $jobrole['status'] ?></td>
                                            <td><?= $jobrole['keterangan'] ?></td>
                                            <td><?= $jobrole['jobdesc'] ?></td>
                                            <td>
                                                <a href="<?= base_url('monitoring/jobroleEdit/' . $jobrole['id_jobrole']) ?>"
                                                    class="btn btn-warning btn-sm">Edit</a>
                                                <button class="btn bg-gradient-danger btn-sm"
                                                    onclick="confirmDelete('<?= $jobrole['id_jobrole'] ?>')">Delete</button>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No Job Role found</td>
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
<script>
    $(document).ready(function() {
        // Initialize DataTable with export options
        $('#jobroleTable').DataTable({});

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