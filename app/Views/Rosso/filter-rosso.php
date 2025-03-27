<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        Filter Data Rosso
                    </h4>
                    <form action="<?= base_url('Monitoring/filterRosso/' . $area_utama) ?>" method="post">
                        <div class="row">
                            <div class="col-4">
                                <label for="start_date">Tanggal Awal:</label>
                                <input class="form-control" type="date" name="tgl_awal" id="tgl_awal" value="" required>
                            </div>
                            <div class="col-4">
                                <label for="end_date">Tanggal Akhir:</label>
                                <input class="form-control" type="date" name="tgl_akhir" id="tgl_akhir" value="" required>
                            </div>
                            <div class="col-4">
                                <label for="">Aksi</label>
                                <button class="form-control btn btn-info" type="submit">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        Data Rosso
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Kartu</th>
                                    <th>Nama Karyawan</th>
                                    <th>Produksi</th>
                                    <th>Perbaikan</th>
                                    <th>Tanggal Input</th>
                                    <th>Area</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                        foreach ($rosso as $b) : ?>
                                <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $b['kode_kartu'] ?></td>
                                        <td><?= $b['nama_karyawan'] ?></td>
                                        <td><?= $b['produksi'] ?></td>
                                        <td><?= $b['perbaikan'] ?></td>
                                        <td><?= $b['tgl_input'] ?></td>
                                        <td><?= $b['area'] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->endSection(); ?>