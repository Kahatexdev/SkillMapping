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
                                Form Input Data Karyawan
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('monitoring/datakaryawan') ?>"
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
                            <label for="kode_kartu">Kode Kartu</label>
                            <input type="text" class="form-control" name="kode_kartu" id="kode_kartu" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="nama_karyawan">Nama Karyawan</label>
                            <input type="text" class="form-control" name="nama_karyawan" id="nama_karyawan" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="shift">Shift</label>
                            <select name="shift" id="shift" class="form-control" required>
                                <option value="">Pilih Shift</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="Non Shift">Non Shift</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="libur">Libur</label>
                            <input type="text" class="form-control" name="libur" id="libur" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="libur_tambahan">Libur Tambahan</label>
                            <input type="text" class="form-control" name="libur_tambahan" id="libur_tambahan" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="warna_baju">Warna Baju</label>
                            <select name="warna_baju" id="warna_baju" class="form-control" required>
                                <option value="">Pilih Warna Baju</option>
                                <option value="Putih">Putih</option>
                                <option value="Biru">Biru</option>
                                <option value="Kuning">Kuning</option>
                                <option value="Pink">Pink</option>
                                <option value="Coklat">Coklat</option>
                                <option value="Hijau">Hijau</option>
                                <option value="Hitam">Hitam</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="status_baju">Status Baju</label>
                            <select name="status_baju" id="status_baju" class="form-control" required>
                                <option value="">Pilih Status Baju</option>
                                <option value="Harian">Harian</option>
                                <option value="Training">Training</option>
                                <option value="Magang">Magang</option>
                                <option value="Karyawan">Karyawan</option>
                                <option value="Staff">Staff</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="tgl_lahir">Tanggal Lahir</label>
                            <input type="date" class="form-control" name="tgl_lahir" id="tgl_lahir" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="tgl_masuk">Tanggal Masuk</label>
                            <input type="date" class="form-control" name="tgl_masuk" id="tgl_masuk" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="bagian">Bagian</label>
                            <select name="bagian" id="bagian" class="form-control" required>
                                <option value="">Pilih Bagian</option>
                                <?php foreach ($bagian as $bagian) : ?>
                                    <option value="<?= $bagian['id_bagian'] ?>">
                                        <?= $bagian['nama_bagian'] . ' - ' . $bagian['area'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="status_aktif">Status Aktif</label>
                            <select name="status_aktif" id="status_aktif" class="form-control" required>
                                <option value="">Pilih Status Aktif</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                            </select>
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