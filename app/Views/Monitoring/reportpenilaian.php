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
                        <table class="table table-hover table-striped" id="table_batch_penilaian">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Bagian</th>
                                    <th>Area</th>
                                    <th>Shift</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($penilaian as $batch) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $batch['nama_bagian'] ?></td>
                                        <td><?= $batch['area'] ?></td>
                                        <td><?= $batch['shift'] ?></td>
                                        <td><?= $batch['bulan'] ?></td>
                                        <td><?= $batch['tahun'] ?></td>
                                        
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



<?php $this->endSection(); ?>