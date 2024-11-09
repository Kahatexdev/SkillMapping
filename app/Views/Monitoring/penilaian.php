<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            Skill Mapping
                            <h4 class="font-weight-bolder">
                                Data Penilaian Mandor
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href="" class="btn bg-gradient-info">
                                    <i class="fas fa-user-tie text-lg opacity-10" aria-hidden="true"></i>
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
                    <h4>
                        Pilih Bacth Penilaian</h4>
                    <form action="<?= base_url('/path/to/check') ?>" method="post">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="bulan">Bulan</label>
                                <select class="form-select" id="bulan" name="bulan" required>
                                    <option value="">Pilih Bulan</option>
                                    <option value="Januari">Januari</option>
                                    <option value="Februari">Februari</option>
                                    <option value="Maret">Maret</option>
                                    <option value="April">April</option>
                                    <option value="Mei">Mei</option>
                                    <option value="Juni">Juni</option>
                                    <option value="Juli">Juli</option>
                                    <option value="Agustus">Agustus</option>
                                    <option value="September">September</option>
                                    <option value="Oktober">Oktober</option>
                                    <option value="November">November</option>
                                    <option value="Desember">Desember</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="year">Tahun</label>
                                <select class="form-select" id="year" name="year" required>
                                    <option value="">Pilih Tahun</option>
                                    <?php for ($i = date('Y'); $i >= 2020; $i--): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="shift">Shift</label>
                                <select class="form-select" id="shift" name="shift" required>
                                    <option value="">Pilih Shift</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="department">Bagian</label>
                                <select class="form-select" id="department" name="department" required>
                                    <option value="">Pilih Bagian</option>
                                    <option value="production">Produksi</option>
                                    <option value="maintenance">Pemeliharaan</option>
                                    <!-- Add other departments as needed -->
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="main_area">Area Utama</label>
                                <select class="form-select" id="main_area" name="main_area" required>
                                    <option value="">Pilih Area Utama</option>
                                    <option value="area_1">Area 1</option>
                                    <option value="area_2">Area 2</option>
                                    <!-- Add other main areas -->
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="main_area">Area</label>
                                <select class="form-select" id="main_area" name="main_area" required>
                                    <option value="">Pilih Area</option>
                                    <option value="area_1">Area 1 A</option>
                                    <option value="area_2">Area 2 B</option>
                                    <!-- Add other main areas -->
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn bg-gradient-info w-100">Cek Data Penilaian</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
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