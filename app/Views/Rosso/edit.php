<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>',
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
                    html: '<?= session()->getFlashdata('error') ?>',
                });
            });
        </script>
    <?php endif; ?>

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
                                Form Edit Summary Rosso
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('monitoring/dataRosso') ?>"
                                    class="btn bg-gradient-secondary btn-sm">
                                    <!-- icon-->
                                    <i class="fas fa-solid fa-arrow-left text-sm opacity-10"></i>
                                    Kembali
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
                <div class="card-body">
                    <form action="<?= base_url('monitoring/rossoUpdate/'. $SummaryRosso['id_sr']) ?>" method="post">


                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group  mb-2">
                                    <label for="nama_karyawan">Nama Karyawan</label>
                                    <input type="text" class="form-control" name="nama_karyawan" id="nama_karyawan" value="<?= $SummaryRosso['nama_karyawan'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="kode_kartu">Kode Kartu</label>
                                    <input type="text" class="form-control" name="kode_kartu" id="kode_kartu" value="<?= $SummaryRosso['kode_kartu'] ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="qty_prod_rosso">Qty Produksi</label>
                                    <input type="text" class="form-control" name="qty_prod_rosso" id="qty_prod_rosso" value="<?= $SummaryRosso['qty_prod_rosso'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="qty_bs">Qty BS</label>
                                    <input type="text" class="form-control" name="qty_bs" id="qty_bs" value="<?= $SummaryRosso['qty_bs'] ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group mb-2">
                                    <label for="tgl_prod_rosso">TGL Produksi Rosso</label>
                                    <input type="date" class="form-control" id="tgl_prod_rosso" name="tgl_prod_rosso" value="<?= $SummaryRosso['tgl_prod_rosso'] ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Simpan -->
                        <button type="submit" class="btn bg-gradient-info btn-sm w-100"><i class="fas fa-save text-sm opacity-10"></i> Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>