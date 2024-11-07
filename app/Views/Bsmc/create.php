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



    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-header">
                    <h5>
                        Form Tambah Data Bs Mesin
                    </h5>
                </div>
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

                        <div class="form-group mb-5">
                            <button type="submit" class="btn bg-gradient-info btn-sm ">Simpan</button>
                            <a href="<?= base_url('monitoring/datakaryawan') ?>"
                                class="btn bg-gradient-dark btn-sm">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php $this->endSection(); ?>