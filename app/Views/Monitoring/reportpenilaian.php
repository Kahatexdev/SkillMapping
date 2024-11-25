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
                                Report Penilaian Mandor
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


    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <!-- table untuk batch penilaian -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="table_report_penilaian">
                            <thead>
                                <tr>
                                    <th>KODE</br>KARTU</th>
                                    <th>NAMA</br>KARYAWAN</th>
                                    <th>SI</br>(Sakit)</th>
                                    <th>MI</br>(Izin)</th>
                                    <th>M</br>(Mangkir )</th>
                                    <th>JML HARI</br>TIDAK MASUK</br>KERJA</th>
                                    <th>PERSENTASE</br>KEHADIRAN</th>
                                    <th>ACCUMULASI</br>ABSENSI</th>
                                    <th>GRADE</br>BEFORE</th>
                                    <th>GRADE</br>AFTER</th>
                                    <th>TRACKING</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($absen as $p) : ?>
                                    <tr>
                                        <td><?= $p['kode_kartu'] ?></td>
                                        <td><?= $p['nama_karyawan'] ?></td>
                                        <td><?= $p['sakit'] ?></td>
                                        <td><?= $p['izin'] ?></td>
                                        <td><?= $p['mangkir'] ?></td>
                                        <td><?= $p['jml_hari_tidak_masuk_kerja'] ?></td>
                                        <td><?= $p['persentase_kehadiran'] ?>%</td>
                                        <td><?= $p['accumulasi_absensi'] ?></td>
                                        <td><?= $p['index_nilai'] ?></td>
                                        <td><?= $p['grade_penilaian'] ?></td>
                                        <td><?= $p['index_nilai'] ?><?= $p['grade_penilaian'] ?></td>
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
<!-- datatable -->
<script>
    $(document).ready(function() {
        // Initialize DataTable with export options
        $('#table_report_penilaian').DataTable({});

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