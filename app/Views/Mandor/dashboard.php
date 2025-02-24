<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-header text-white">
            <h4 class="mb-0">Status Penilaian Karyawan - Area <?= $area ?></h4>
        </div>
    </div>

    <!-- Select Option untuk Periode -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="form-group">
                <label for="periodeSelect">Pilih Periode</label>
                <select id="periodeSelect" class="form-control">
                    <?php if (!empty($periode)) : ?>
                        <?php foreach ($periode as $p) : ?>
                            <option value="<?= $p['id_periode']; ?>">
                                <?= $p['nama_batch']; ?> - Periode <?= $p['nama_periode']; ?> (<?= date('d/M/Y', strtotime($p['start_date'])); ?> - <?= date('d/M/Y', strtotime($p['end_date'])); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <option value="1">Periode 1</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabel Evaluasi Karyawan -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="evaluationTable" class="table table-striped table-bordered">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama Karyawan</th>
                            <th>Bagian</th>
                            <th>Area</th>
                            <th>Status Evaluasi</th>
                        </tr>
                    </thead>
                    <tbody id="evaluationBody">
                        <tr>
                            <td colspan="5" class="text-center">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Script AJAX untuk memuat data berdasarkan periode yang dipilih -->
<script>
    $(document).ready(function() {
        function loadEvaluationData() {
            var id_periode = $('#periodeSelect').val();
            var area = "<?= $area ?>";

            $.ajax({
                url: "<?= base_url($role . '/evaluasiKaryawan') ?>/" + id_periode + "/" + area,
                method: "GET",
                dataType: "json",
                success: function(data) {
                    console.log("Data periode " + id_periode + ":", data);
                    var html = "";
                    var no = 1;

                    if (data.length > 0) {
                        data.forEach(function(row) {
                            html += "<tr>";
                            html += "<td class='text-center'>" + no + "</td>";
                            html += "<td>" + row.nama_karyawan + "</td>";
                            html += "<td>" + row.nama_bagian + "</td>";
                            html += "<td>" + row.area + "</td>";
                            html += "<td class='text-center'>" +
                                (row.status == 'Sudah Dinilai' ? '<span class="badge bg-info">Sudah Dinilai</span>' : '<span class="badge bg-danger">Belum Dinilai</span>') +
                                "</td>";
                            html += "</tr>";

                            no++;
                        });
                    } else {
                        html += "<tr>";
                        html += "<td colspan='5' class='text-center'>Tidak ada data evaluasi karyawan</td>";
                        html += "</tr>";
                    }

                    $('#evaluationBody').html(html);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error: " + status + " - " + error);
                    $('#evaluationBody').html("<tr><td colspan='5' class='text-center'>Gagal memuat data evaluasi karyawan</td></tr>");
                }
            });
        }

        // Load data evaluasi karyawan saat halaman pertama kali di-load
        loadEvaluationData();

        // Load data evaluasi karyawan saat periode diubah
        $('#periodeSelect').change(function() {
            loadEvaluationData();
        });



    });
</script>

<?php $this->endSection(); ?>