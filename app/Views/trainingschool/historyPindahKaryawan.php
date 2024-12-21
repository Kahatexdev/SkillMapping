<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Skill Mapping</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Data History Pindah Karyawan
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <a href="<?= base_url('TrainingSchool/reportHistoryPindahKaryawan') ?>"
                                class="btn bg-gradient-primary me-2">
                                <!-- icon download -->
                                <i class="fas fa-file-excel text-lg opacity-10" aria-hidden="true"></i>
                                Export Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <!-- <div class="d-flex justify-content-between">

                        <a href="<?= base_url('TrainingSchool/reportHistoryPindahKaryawan'); ?>" class="btn btn-success">Export Excel</a>
                    </div> -->
                    <div class="table-responsive">
                        <table id="example1" class="table table-striped table-hover table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Karyawan</th>
                                    <th>Bagian Asal</th>
                                    <th>Bagian Baru</th>
                                    <th>Tanggal Pindah</th>
                                    <th>Keterangan</th>
                                    <th>Diupdate Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($historyPindahKaryawan as $row): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= esc($row['nama_karyawan']); ?></td>
                                        <td><?= esc($row['bagian_asal']) . '-' . esc($row['area_utama_asal']) . '-' . esc($row['area_asal']); ?></td>
                                        <td><?= esc($row['bagian_baru']) . '-' . esc($row['area_utama_baru']) . '-' . esc($row['area_baru']); ?></td>
                                        <td><?= esc($row['tgl_pindah']); ?></td>
                                        <td><?= esc($row['keterangan']); ?></td>
                                        <td><?= esc($row['updated_by']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
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
        // Initialize DataTable with export options
        $('#example1').DataTable({});
    });
</script>
<?php $this->endSection(); ?>