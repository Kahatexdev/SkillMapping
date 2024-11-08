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
                                    <!-- icon data bs mesin -->
                                    <i class="fas fa-user-tie text-lg opacity-10" aria-hidden="true"></i>
                                </a>
                                Form Input Data BS Mesin
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('monitoring/dataBsmc') ?>"
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
                    <form action="<?= base_url('monitoring/karyawanStore') ?>" method="post">
                        <div class="form-group mb-2">
                            <label for="id_karyawan">Nama Karyawan</label>
                            <select name="id_karyawan" id="id_karyawan" class="form-control" required>
                                <option value="">Pilih karyawan</option>
                                <?php foreach ($karyawan as $karyawan) : ?>
                                    <option value="<?= $karyawan['id_karyawan'] ?>">
                                        <?= $karyawan['kode_kartu'] . ' - ' . $karyawan['nama_karyawan'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" class="form-control" name="tanggal" id="tanggal" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="no_model">Nomor Model</label>
                            <input type="text" class="form-control" name="no_model" id="no_model" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="inisial">Inisial</label>
                            <input type="text" class="form-control" name="inisial" id="inisial" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="qty_prod_mc">Qty Prod Mc</label>
                            <input type="number" class="form-control" name="qty_prod_mc" id="qty_prod_mc" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="qty_bs">Qty Bs</label>
                            <input type="number" class="form-control" name="qty_bs" id="qty_bs" required>
                        </div>

                        <!-- Tombol Simpan -->
                        <button type="submit" class="btn bg-gradient-info btn-sm w-100"><i class="fas fa-save text-sm opacity-10"></i> Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php $this->endSection(); ?>