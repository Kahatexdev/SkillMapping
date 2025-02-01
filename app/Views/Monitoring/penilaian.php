<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>
<link href="<?= base_url('assets/css/select2.min.css') ?>" rel="stylesheet" />
<div class="container-fluid py-4">
    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4>Form Penilaian Karyawan</h4>
                    <form action="<?= base_url('Monitoring/penilaianCreate') ?>" method="post">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="id_periode">Batch Penilaian</label>
                                <select class="form-select" id="id_periode" name="id_periode" required>
                                    <option value="">Pilih Batch Penilaian</option>
                                    <?php foreach ($periode as $b) : ?>
                                        <option value="<?= $b['id_periode'] ?>">Periode <?= $b['nama_periode'] ?> - <?= $b['nama_batch'] ?> (<?= $b['start_date'] ?> s/d <?= $b['end_date'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="nama_bagian">Bagian</label>
                                <select class="form-select" id="nama_bagian" name="nama_bagian" required>
                                    <option value="">Pilih Bagian</option>
                                    <?php foreach ($namabagian as $b): ?>
                                        <option value="<?= $b['nama_bagian'] ?>"><?= $b['nama_bagian'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="area_utama">Area Utama</label>
                                <select class="form-select" id="area_utama" name="area_utama" required>
                                    <option value="">Pilih Area Utama</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="area">Area</label>
                                <select class="form-select" id="area" name="area">
                                    <option value="">Pilih Area</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="karyawan">Pilih Karyawan</label>
                                <select class="form-select select2-multiple" id="karyawan" name="karyawan[]" multiple required>
                                    <option value="">Pilih Karyawan</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" class="form-control" id="id_jobrole" name="id_jobrole" required>
                        <button type="submit" class="btn bg-gradient-info w-100">Buat Form Penilaian</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/js/select2.min.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Event listener untuk nama_bagian
        document.getElementById('nama_bagian').addEventListener('change', function() {
            const namaBagian = this.value;
            const areaUtamaSelect = document.getElementById('area_utama');
            const areaSelect = document.getElementById('area');
            const karyawanSelect = document.getElementById('karyawan');

            // Reset dependent dropdowns
            areaUtamaSelect.innerHTML = '<option value="">Pilih Area Utama</option>';
            areaSelect.innerHTML = '<option value="">Pilih Area</option>';
            karyawanSelect.innerHTML = '<option value="">Pilih Karyawan</option>';

            if (namaBagian) {
                // Fetch area utama berdasarkan nama_bagian
                fetch(`<?= base_url('Monitoring/getAreaUtama') ?>?nama_bagian=${namaBagian}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.area_utama; // Gunakan properti 'area_utama'
                            option.textContent = item.area_utama; // Gunakan properti 'area_utama'
                            areaUtamaSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching area utama:', error));
            }
        });

        // Event listener untuk area_utama
        document.getElementById('area_utama').addEventListener('change', function() {
            const namaBagian = document.getElementById('nama_bagian').value;
            const areaUtama = this.value;
            const areaSelect = document.getElementById('area');
            const karyawanSelect = document.getElementById('karyawan');

            // Reset dependent dropdown
            areaSelect.innerHTML = '<option value="">Pilih Area</option>';
            karyawanSelect.innerHTML = '<option value="">Pilih Karyawan</option>';

            if (namaBagian && areaUtama) {
                // Fetch area berdasarkan nama_bagian dan area_utama
                fetch(`<?= base_url('Monitoring/getArea') ?>?nama_bagian=${namaBagian}&area_utama=${areaUtama}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.area; // Sesuaikan properti dari respons API
                            option.textContent = item.area; // Sesuaikan properti dari respons API
                            areaSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching area:', error));
            }
        });

        // Event listener untuk area
        document.getElementById('area').addEventListener('change', function() {
            const namaBagian = document.getElementById('nama_bagian').value;
            const areaUtama = document.getElementById('area_utama').value;
            const area = this.value;
            const karyawanSelect = document.getElementById('karyawan');

            // Reset karyawan dropdown
            karyawanSelect.innerHTML = '<option value="">Pilih Karyawan</option>';

            if (namaBagian && areaUtama && area) {
                // Fetch karyawan berdasarkan nama_bagian, area_utama, dan area
                fetch(`<?= base_url('Monitoring/getKaryawan') ?>?nama_bagian=${namaBagian}&area_utama=${areaUtama}&area=${area}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.id_karyawan; // Sesuaikan properti dari respons API
                            option.textContent = item.nama_karyawan + ' - ' + item.kode_kartu; // Sesuaikan properti dari respons API
                            karyawanSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching karyawan:', error));
            }
        });
    });

    $(document).ready(function() {
        // Aktifkan library select2 untuk multiple select
        $('#karyawan').select2({
            placeholder: "Pilih Karyawan",
            allowClear: true
        });

    });
</script>
<script>
    $(document).ready(function() {
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