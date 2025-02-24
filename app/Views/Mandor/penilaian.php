<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>
<link href="<?= base_url('assets/css/select2.min.css') ?>" rel="stylesheet" />
<div class="container-fluid py-4">
    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4>Form Penilaian Karyawan</h4>
                    <form action="<?= base_url('Mandor/penilaianCreate') ?>" method="post">
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

<div class="modal fade" id="modal-instruksi" tabindex="-1" aria-labelledby="modal-instruksi" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Petunjuk Pengisian Nilai</h5>
            </div>
            <div class="modal-body">
                <!-- Step 1: Instruksi Umum -->
                <div class="instruksi-step" id="instruksi-step-1">
                    <p><strong>Langkah 1:</strong> Pilih batch penilaian untuk membuat form penilaian.</p>
                    <p><strong>Langkah 2:</strong> Pilih bagian yang akan dinilai.</p>
                    <p><strong>Langkah 3:</strong> Pilih area utama penilaian.</p>
                    <p><strong>Langkah 4:</strong> Pilih area penilaian.</p>
                    <p><strong>Langkah 5:</strong> Pilih karyawan yang akan dinilai.</p>
                    <p><strong>Langkah 6:</strong> Klik tombol <em>Buat Form Penilaian</em> untuk memulai proses penilaian.</p>
                </div>
                <!-- Step 2: Pengertian Batch dan Contoh Batch 1 -->
                <div class="instruksi-step d-none" id="instruksi-step-2">
                    <h6>Pengertian Batch</h6>
                    <p>Batch adalah periode penilaian yang digunakan untuk mengevaluasi karyawan. Dalam sistem Skill Mapping, satu batch terdiri dari tiga periode penilaian.</p>
                    <p><strong>Contoh: BATCH 1 2025</strong></p>
                    <ul>
                        <li><strong>Periode Awal:</strong> 21 Januari 2025 – 28 Februari 2025</li>
                        <li><strong>Periode Tengah:</strong> 1 Maret 2025 – 31 Maret 2025</li>
                        <li><strong>Periode Akhir:</strong> 1 April 2025 – 20 April 2025</li>
                    </ul>
                </div>
                <!-- Step 3: Contoh Batch 2 dan Batch 3 -->
                <div class="instruksi-step d-none" id="instruksi-step-3">
                    <p><strong>BATCH 2 2025</strong></p>
                    <ul>
                        <li><strong>Periode Awal:</strong> 21 April 2025 – 31 Mei 2025</li>
                        <li><strong>Periode Tengah:</strong> 1 Juni 2025 – 30 Juni 2025</li>
                        <li><strong>Periode Akhir:</strong> 1 Juli 2025 – 20 Juli 2025</li>
                    </ul>
                    <p><strong>BATCH 3 2025</strong></p>
                    <ul>
                        <li><strong>Periode Awal:</strong> 21 Juli 2025 – 31 Agustus 2025</li>
                        <li><strong>Periode Tengah:</strong> 1 September 2025 – 30 September 2025</li>
                        <li><strong>Periode Akhir:</strong> 1 Oktober 2025 – 20 Oktober 2025</li>
                    </ul>
                </div>
                <!-- Step 4: Contoh Batch 4 -->
                <div class="instruksi-step d-none" id="instruksi-step-4">
                    <p><strong>BATCH 4 2025</strong></p>
                    <ul>
                        <li><strong>Periode Awal:</strong> 21 Oktober 2025 – 30 November 2025</li>
                        <li><strong>Periode Tengah:</strong> 1 Desember 2025 – 31 Desember 2025</li>
                        <li><strong>Periode Akhir:</strong> 1 Januari 2026 – 20 Januari 2026</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btnBack">Kembali</button>
                <button type="button" class="btn btn-info" id="btnNext">Selanjutnya</button>
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
                fetch(`<?= base_url('Mandor/getAreaUtama') ?>?nama_bagian=${namaBagian}`)
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
                fetch(`<?= base_url('Mandor/getArea') ?>?nama_bagian=${namaBagian}&area_utama=${areaUtama}`)
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
                fetch(`<?= base_url('Mandor/getKaryawan') ?>?nama_bagian=${namaBagian}&area_utama=${areaUtama}&area=${area}`)
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var myModal = new bootstrap.Modal(document.getElementById("modal-instruksi"));
        myModal.show();
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var currentStep = 1;
        var totalSteps = 4; // Jumlah total step
        var btnBack = document.getElementById('btnBack');
        var btnNext = document.getElementById('btnNext');

        // Sembunyikan tombol "Kembali" di step pertama
        btnBack.style.display = 'none';

        btnNext.addEventListener('click', function() {
            if (currentStep < totalSteps) {
                // Sembunyikan step saat ini
                document.getElementById('instruksi-step-' + currentStep).classList.add('d-none');
                // Naikkan langkah
                currentStep++;
                // Tampilkan step berikutnya
                document.getElementById('instruksi-step-' + currentStep).classList.remove('d-none');

                // Tampilkan tombol "Kembali" jika sudah melewati step pertama
                if (currentStep > 1) {
                    btnBack.style.display = 'inline-block';
                }

                // Ubah teks tombol jika sudah di step terakhir
                if (currentStep === totalSteps) {
                    btnNext.textContent = 'Selesai';
                } else {
                    btnNext.textContent = 'Selanjutnya';
                }
            } else {
                // Jika sudah step terakhir, tutup modal
                var modalEl = document.getElementById('modal-instruksi');
                var modalInstance = bootstrap.Modal.getInstance(modalEl);
                modalInstance.hide();
            }
        });

        btnBack.addEventListener('click', function() {
            if (currentStep > 1) {
                // Sembunyikan step saat ini
                document.getElementById('instruksi-step-' + currentStep).classList.add('d-none');
                // Turunkan langkah
                currentStep--;
                // Tampilkan step sebelumnya
                document.getElementById('instruksi-step-' + currentStep).classList.remove('d-none');

                // Ubah teks tombol Next jika tidak lagi di step terakhir
                btnNext.textContent = 'Selanjutnya';

                // Sembunyikan tombol "Kembali" jika sudah kembali ke step pertama
                if (currentStep === 1) {
                    btnBack.style.display = 'none';
                }
            }
        });
    });
</script>
<?php $this->endSection(); ?>