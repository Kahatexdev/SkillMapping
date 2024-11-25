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
                                    <!-- Icon Data Absen -->
                                    <i class="fas fa-solid fa-2x fa-user-clock"></i>
                                </a>
                                Data Absen
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <!-- download report excel -->
                                <!-- <a href="<?= base_url('monitoring/absenReport/') ?>" class="btn bg-gradient-success me-2">
                                    <i class="fas fa-file-excel text-lg me-2"></i>Report Absen
                                </a>
                                <a href="<?= base_url('monitoring/absenCreate') ?>" class="btn bg-gradient-info me-2">
                                    <i class="fas fa-plus text-lg opacity-10 me-1" aria-hidden="true"></i>
                                    Data Absen
                                </a>
                                <div> &nbsp;</div> -->
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
                        Tabel Data Absen
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="absenTable" class="table table-striped table-hover table-bordered">
                            <thead>
                                <th>No</th>
                                <th>Nama Karyawan</th>
                                <th>Bulan</th>
                                <th>SI(Sakit)</th>
                                <th>MI(Izin)</th>
                                <th>M(Mangkir )</th>
                                <th>T(Cuti)</th>
                                <th>Input By</th>
                            </thead>
                            <tbody>

                                <?php if (!empty($absen)) : ?>
                                    <?php foreach ($absen as $absen) : ?>
                                        <tr>
                                            <td><?= $absen['id_absen'] ?></td>
                                            <td><?= $absen['nama_karyawan'] ?></td>
                                            <td><?= $absen['bulan'] ?></td>
                                            <?php if ($absen['sakit'] == 0) : ?>
                                                <td></td>
                                            <?php else : ?>
                                                <td><?= $absen['sakit'] ?></td>
                                            <?php endif; ?>
                                            <?php if ($absen['izin'] == 0) : ?>
                                                <td></td>
                                            <?php else : ?>
                                                <td><?= $absen['izin'] ?></td>
                                            <?php endif; ?>
                                            <?php if ($absen['mangkir'] == 0) : ?>
                                                <td></td>
                                            <?php else : ?>
                                                <td><?= $absen['mangkir'] ?></td>
                                            <?php endif; ?>
                                            <?php if ($absen['cuti'] == 0) : ?>
                                                <td></td>
                                            <?php else : ?>
                                                <td><?= $absen['cuti'] ?></td>
                                            <?php endif; ?>
                                            <td><?= $absen['username'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="9" class="text-center">Data tidak ditemukan</td>
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