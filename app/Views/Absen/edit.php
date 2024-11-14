<?php $this->extend('Monitoring/layout'); ?>
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
                                    <!-- Icon Data Absen -->
                                    <i class="fas fa-solid fa-2x fa-user-clock"></i>
                                </a>
                                Edit Data Absen
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('monitoring/dataAbsen') ?>" class="btn bg-gradient-secondary btn-sm me-2">
                                    <!-- Icon Kembali -->
                                    <i class="fas fa-arrow-left text-lg opacity-10" aria-hidden="true"></i>
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
                    <form action="<?= base_url('monitoring/absenUpdate/' . $data['id_absen']) ?>" method="post">

                        <div class="form-group mb-2">
                            <label for="id_karyawan">Nama Karyawan</label>
                            <select name="id_karyawan" id="id_karyawan" class="form-control" required>
                                <option value="">Pilih Karyawan</option>
                                <?php foreach ($datajoin as $karyawan) : ?>
                                    <option value="<?= $karyawan['id_karyawan'] ?>"
                                        <?= $data['id_karyawan'] == $karyawan['id_karyawan'] ? 'selected' : '' ?>>
                                        <?= $karyawan['id_karyawan'] ?> - <?= $karyawan['nama_karyawan'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            <label for="tanggal">Tanggal Masuk</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control"
                                value="<?= $data['tanggal'] ?>" required>
                        </div>

                        <div class="form-group mb-2">
                            <label for="izin">Izin</label>
                            <input type="number" name="izin" id="izin" class="form-control" value="<?= $data['izin'] ?>">
                        </div>

                        <div class="form-group mb-2">
                            <label for="sakit">Sakit</label>
                            <input type="number" name="sakit" id="sakit" class="form-control" value="<?= $data['sakit'] ?>">
                        </div>

                        <div class="form-group mb-2">
                            <label for="mangkir">Mangkir</label>
                            <input type="number" name="mangkir" id="mangkir" class="form-control"
                                value="<?= $data['mangkir'] ?>">
                        </div>

                        <div class="form-group mb-2">
                            <label for="cuti">Cuti</label>
                            <input type="number" name="cuti" id="cuti" class="form-control" value="<?= $data['cuti'] ?>">
                        </div>
                        <input type="hidden" name="id_user" value="<?= $data['id_user'] ?>">
                        <input type="hidden" name="id_absen" value="<?= $data['id_absen'] ?>">
                        <button type="submit" class="btn bg-gradient-info btn-sm w-100">
                            <i class="fas fa-save text-lg opacity-10" aria-hidden="true"></i>
                            Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>