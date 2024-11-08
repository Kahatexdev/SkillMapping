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
                                    <!-- Icon Data Bagian -->
                                    <i class="fas fa-solid fa-2x fa-briefcase"></i>
                                </a>
                                Data Bagian
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('monitoring/bagianCreate') ?>"
                                    class="btn bg-gradient-info btn-sm">
                                    <!-- Icon Tambah Bagian-->
                                    <i class="fas fa-plus text-lg opacity-10 me-1" aria-hidden="true"></i>
                                    Data Bagian
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
                        Tabel Data Bagian
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="bagianTable" class="table table-striped table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Bagian</th>
                                    <th>Area Utama</th>
                                    <th>Area</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($bagian)) : ?>
                                    <?php foreach ($bagian as $bagian) : ?>
                                        <tr>
                                            <td><?= $bagian['id_bagian'] ?></td>
                                            <td><?= $bagian['nama_bagian'] ?></td>
                                            <td><?= $bagian['area_utama'] ?></td>
                                            <td><?= $bagian['area'] ?></td>
                                            <td><?= $bagian['keterangan'] ?></td>

                                            <td>
                                                <a href="<?= base_url('monitoring/jobroleCreate') ?>" class="btn bg-gradient-info btn-sm">
                                                    <i class="fas fa-plus text-lg opacity-10" aria-hidden="true"></i>
                                                    Job Role
                                                </a>
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
                window.location.href = "<?= base_url('monitoring/bagianDelete/') ?>" + id;
            }
        })
    }
</script>
<script>
    $(document).ready(function() {
        // Initialize DataTable with export options
        $('#bagianTable').DataTable({});

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