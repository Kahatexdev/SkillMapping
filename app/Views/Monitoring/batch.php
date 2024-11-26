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
                                    <!-- icon data batch -->
                                    <i class="fas fa-database text-lg opacity-10 me-1" aria-hidden="true"></i>
                                </a>
                                Data Batch
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('monitoring/batchCreate') ?>"
                                    class="btn bg-gradient-info btn-sm">
                                    <!-- icon tambah batch-->
                                    <i class="fas fa-plus text-lg opacity-10 me-1" aria-hidden="true"></i>
                                    Data Batch
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
                        Tabel Data Batch
                    </h4>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="batchTable" class="table table-striped table-hover table-bordered w-100">
                            <thead>
                                <th>No</th>
                                <th>Nama Batch</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                <?php if (!empty($batch)) : ?>
                                    <?php foreach ($batch as $batch) : ?>
                                        <tr>
                                            <td><?= $batch['id_batch'] ?></td>
                                            <td><?= $batch['nama_batch'] ?></td>
                                            <td>
                                                <a href="<?= base_url('monitoring/batchEdit/' . $batch['id_batch']) ?>"
                                                    class="btn bg-gradient-warning btn-sm">
                                                    <!-- icon edit -->
                                                    <i class="fas fa-edit text-lg opacity-10" aria-hidden="true"></i>
                                                </a>
                                                <button onclick="confirmDelete(<?= $batch['id_batch'] ?>)"
                                                    class="btn bg-gradient-danger btn-sm">
                                                    <!-- icon hapus -->
                                                    <i class="fas fa-trash text-lg opacity-10" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else : ?>

                                    <tr>
                                        <td colspan="4" class="text-center">No Batch found</td>
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
                window.location.href = "<?= base_url('monitoring/batchDelete/') ?>" + id;
            }
        })
    }
</script>
<script>
    $(document).ready(function() {
        // Initialize DataTable with export options
        $('#batchTable').DataTable({});

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

<?php $this->endSection(); ?>