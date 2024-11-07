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



    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-header">
                    <h5>
                        Form Edit Data Karyawan
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('monitoring/karyawanUpdate/' . $karyawan['id_karyawan']) ?>"
                        method="post">
                        <div class="form-group mb-2">
                            <label for="kode_kartu">Kode Kartu</label>
                            <input type="text" class="form-control" name="kode_kartu" id="kode_kartu"
                                value="<?= $karyawan['kode_kartu'] ?>" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="nama_karyawan">Nama Karyawan</label>
                            <input type="text" class="form-control" name="nama_karyawan" id="nama_karyawan"
                                value="<?= $karyawan['nama_karyawan'] ?>" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="shift">Shift</label>
                            <select name="shift" id="shift" class="form-control" required>
                                <option value="">Pilih Shift</option>
                                <option value="A" <?= $karyawan['shift'] == 'A' ? 'selected' : '' ?>>Pagi</option>
                                <option value="B" <?= $karyawan['shift'] == 'B' ? 'selected' : '' ?>>Siang</option>
                                <option value="C" <?= $karyawan['shift'] == 'C' ? 'selected' : '' ?>>Malam</option>
                                <option value="Non Shift" <?= $karyawan['shift'] == 'Non Shift' ? 'selected' : '' ?>>Non
                                    Shift</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" <?= $karyawan['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki
                                </option>
                                <option value="P" <?= $karyawan['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan
                                </option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="libur">Libur</label>
                            <input type="text" class="form-control" name="libur" id="libur"
                                value="<?= $karyawan['libur'] ?>" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="libur_tambahan">Libur Tambahan</label>
                            <input type="text" class="form-control" name="libur_tambahan" id="libur_tambahan"
                                value="<?= $karyawan['libur_tambahan'] ?>" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="warna_baju">Warna Baju</label>
                            <select name="warna_baju" id="warna_baju" class="form-control" required>
                                <option value="">Pilih Warna Baju</option>
                                <option value="Merah" <?= $karyawan['warna_baju'] == 'Merah' ? 'selected' : '' ?>>Merah
                                </option>
                                <option value="Biru" <?= $karyawan['warna_baju'] == 'Biru' ? 'selected' : '' ?>>Biru
                                </option>
                                <option value="Hijau" <?= $karyawan['warna_baju'] == 'Hijau' ? 'selected' : '' ?>>Hijau
                                </option>
                                <option value="Kuning" <?= $karyawan['warna_baju'] == 'Kuning' ? 'selected' : '' ?>>
                                    Kuning
                                </option>
                                <option value="Putih" <?= $karyawan['warna_baju'] == 'Putih' ? 'selected' : '' ?>>Putih
                                </option>
                                <option value="Hitam" <?= $karyawan['warna_baju'] == 'Hitam' ? 'selected' : '' ?>>Hitam
                                </option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="status_baju">Status Baju</label>
                            <select name="status_baju" id="status_baju" class="form-control" required>
                                <option value="">Pilih Status Baju</option>
                                <option value="Harian" <?= $karyawan['status_baju'] == 'Harian' ? 'selected' : '' ?>>
                                    Harian
                                </option>
                                <option value="Training"
                                    <?= $karyawan['status_baju'] == 'Training' ? 'selected' : '' ?>>
                                    Training</option>
                                <option value="Magang" <?= $karyawan['status_baju'] == 'Magang' ? 'selected' : '' ?>>
                                    Magang
                                </option>
                                <option value="Karyawan"
                                    <?= $karyawan['status_baju'] == 'Karyawan' ? 'selected' : '' ?>>
                                    Karyawan</option>
                                <option value="Staff" <?= $karyawan['status_baju'] == 'Staff' ? 'selected' : '' ?>>Staff
                                </option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="tgl_lahir">Tanggal Lahir</label>
                            <input type="date" class="form-control" name="tgl_lahir" id="tgl_lahir"
                                value="<?= $karyawan['tgl_lahir'] ?>" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="tgl_masuk">Tanggal Masuk</label>
                            <input type="date" class="form-control" name="tgl_masuk" id="tgl_masuk"
                                value="<?= $karyawan['tgl_masuk'] ?>" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="bagian">Bagian</label>
                            <select name="bagian" id="bagian" class="form-control" required>
                                <option value="">Pilih Bagian</option>
                                <?php foreach ($bagian as $bagian) : ?>
                                <option value="<?= $bagian['id_bagian'] ?>"
                                    <?= $karyawan['id_bagian'] == $bagian['id_bagian'] ? 'selected' : '' ?>>
                                    <?= $bagian['nama_bagian'] . ' - ' . $bagian['area'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="status_aktif">Status Aktif</label>
                            <select name="status_aktif" id="status_aktif" class="form-control" required>
                                <option value="">Pilih Status Aktif</option>
                                <option value="Aktif" <?= $karyawan['status_aktif'] == 'Aktif' ? 'selected' : '' ?>>
                                    Aktif
                                </option>
                                <option value="Tidak Aktif"
                                    <?= $karyawan['status_aktif'] == 'Tidak Aktif' ? 'selected' : '' ?>>Tidak Aktif
                                </option>
                            </select>
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
<?php $this->endSection(); ?>