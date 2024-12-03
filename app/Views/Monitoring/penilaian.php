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
                        Pilih Batch Penilaian</h4>
                    <form action="<?= base_url('Monitoring/penilaianCreate') ?>" method="get">
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
                            <input type="hidden" class="form-control" id="id_jobrole" name="id_jobrole" required>
                        </div>
                        <button type="submit" class="btn bg-gradient-info w-100">Buat Batch Penilaian</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <!-- table untuk batch penilaian -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="table_batch_penilaian">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Bagian</th>
                                    <th>Area Utama</th>
                                    <th>Area</th>
                                    <th>Periode</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($penilaian)) : ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($penilaian as $periode) : ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $periode['nama_bagian'] ?></td>
                                            <td><?= $periode['area_utama'] ?></td>
                                            <td><?= $periode['area'] ?></td>
                                            <td><?= $periode['nama_periode'] ?></td>
                                            <td>
                                                <a href="<?= base_url('Monitoring/penilaianDetail/' . $periode['id_bagian'] . '/' . $periode['id_periode'] . '/' . $periode['id_jobrole']) ?>" class="btn bg-gradient-info btn-sm">
                                                    <i class="fas fa-eye text-lg opacity-10" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data penilaian.</td>
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

<script>
    // datatable
    $(document).ready(function() {
        $('#table_batch_penilaian').DataTable();
    });
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
    $(document).ready(function() {
        // Ambil Area Utama berdasarkan Nama Bagian
        $('#nama_bagian').change(function() {
            var nama_bagian = $(this).val();

            // Reset area_utama dan area ketika nama_bagian diubah
            $("#area_utama").empty().append("<option value=''>Pilih Area Utama</option>");
            $("#area").empty().append("<option value=''>Pilih Area</option>");
            $("#id_jobrole").val(''); // Reset id_jobrole field

            $.ajax({
                url: '<?= base_url('Monitoring/getAreaUtama') ?>',
                type: 'post',
                data: {
                    nama_bagian: nama_bagian
                },
                dataType: 'json',
                success: function(response) {
                    response.forEach(function(item) {
                        $("#area_utama").append("<option value='" + item['area_utama'] +
                            "'>" + item['area_utama'] + "</option>");
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error Area Utama:", xhr.responseText);
                    alert('Terjadi kesalahan saat mengambil data Area Utama.');
                }
            });
        });

        // Ambil Area berdasarkan Area Utama dan Nama Bagian
        function updateArea() {
            var area_utama = $('#area_utama').val();
            var nama_bagian = $('#nama_bagian').val();

            $.ajax({
                url: '<?= base_url('Monitoring/getArea') ?>',
                type: 'post',
                data: {
                    area_utama: area_utama,
                    nama_bagian: nama_bagian
                },
                dataType: 'json',
                success: function(response) {
                    $("#area").empty().append("<option value=''>Pilih Area</option>");
                    response.forEach(function(item) {
                        $("#area").append("<option value='" + item['area'] + "'>" + item[
                            'area'] + "</option>");
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error Area:", xhr.responseText);
                    alert('Terjadi kesalahan saat mengambil data Area.');
                }
            });
        }

        // Ambil Job Role berdasarkan Area, Area Utama, dan Nama Bagian
        function updateJobRole() {
            var area_utama = $('#area_utama').val();
            var nama_bagian = $('#nama_bagian').val();
            var area = $('#area').val();

            $.ajax({
                url: '<?= base_url('Monitoring/getJobRole') ?>',
                type: 'post',
                data: {
                    area_utama: area_utama,
                    nama_bagian: nama_bagian,
                    area: area
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    // Set value of id_jobrole field
                    $("#id_jobrole").val(response['id_jobrole']);
                },
                error: function(xhr, status, error) {
                    console.error("Error Job Role:", xhr.responseText);
                    alert('Terjadi kesalahan saat mengambil data Job Role.');
                }
            });
        }

        $('#area_utama').change(updateArea);
        $('#nama_bagian').change(updateArea);
        $('#area').change(updateJobRole);
    });
</script>



<?php $this->endSection(); ?>