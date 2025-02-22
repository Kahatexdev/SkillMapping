<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <!-- Header Monitoring -->
            <div class="card card-frame mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bolder">Monitoring Penilaian Karyawan</h5>
                    </div>
                </div>
            </div>
            <!-- Tabel Monitoring -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="cekPenilaianTable" class="table table-striped table-hover table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Area</th>
                                    <th>Jumlah Karyawan</th>
                                    <th>Sudah Dinilai</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($cekPenilaian)) : ?>
                                    <?php foreach ($cekPenilaian as $mandor) : ?>
                                        <tr>
                                            <td><?= esc($mandor['username']); ?></td>
                                            <td><?= esc($mandor['total_karyawan']); ?></td>
                                            <td><?= esc($mandor['total_penilaian']); ?></td>
                                            <td>
                                                <?php if ($mandor['total_penilaian'] >= $mandor['total_karyawan']) : ?>
                                                    <span class="badge bg-info">Selesai</span>
                                                <?php else : ?>
                                                    <span class="badge bg-danger">Belum Selesai</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="4">Tidak ada data</td>
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
    $(document).ready(function() {
        $('#cekPenilaianTable').DataTable({});
    });
</script>

<?php $this->endSection(); ?>