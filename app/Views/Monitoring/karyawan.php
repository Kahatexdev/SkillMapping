<?php $this->extend('Monitoring/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?= session()->getFlashdata('success') ?>',
                });
            });
        </script>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '<?= session()->getFlashdata('error') ?>',
                });
            });
        </script>
    <?php endif; ?>



    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-header">
                    <h5>
                        Data order
                    </h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-success btn-sm import-btn" data-toggle="modal"
                        data-target="#EditModal" data-id="">
                        Input Produksi
                    </button>
                    <div class="table-responsive">
                        <table id="dataTable" class="display">
                            <thead>
                                <th>No</th>
                                <th>NIK</th>
                                <th>Kode Kartu</th>
                                <th>Nama Karyawan</th>
                                <th>Tgl Masuk</th>
                                <th>Jenis Kelamin</th>
                                <th>Bagian</th>
                            </thead>
                            <tbody>
                                <?php foreach ($karyawan as $karyawan) : ?>
                                    <tr>
                                        <td><?= $karyawan['kode_kartu'] ?></td>
                                        <td><?= $karyawan['nama_karyawan'] ?></td>
                                        <td><?= $karyawan['tgl_masuk'] ?></td>
                                        <td><?= $karyawan['jenis_kelamin'] ?></td>
                                        <td><?= $karyawan['id_bagian'] ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModal"
        aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Input Data Produksi</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body align-items-center">

                    <div class="row mt-2">
                        <div class="col-12 pl-0">

                            <form action="<?= base_url('area/inputproduksi') ?>" id="modalForm" method="POST"
                                enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="seam" class="col-form-label">No Model:</label>

                                </div>
                                <div class="form-group">
                                    <label for="seam" class="col-form-label">Inisial:</label>
                                    <select class="form-control" id="id_inisial" name="id_inisial"></select>
                                </div>
                                <div class="form-group">
                                    <label for="seam" class="col-form-label">Qty Produksi:</label>
                                    <input type="number" name="qty_production" id="" class="form-control"
                                        oninput="this.value = this.value.toUpperCase()">
                                </div>
                                <div class="form-group">
                                    <label for="seam" class="col-form-label">Bs Produksi:</label>
                                    <input type="number" name="bs_mc" id="" class="form-control"
                                        oninput="this.value = this.value.toUpperCase()">
                                </div>
                                <div class="form-group">
                                    <label for="seam" class="col-form-label">Tanggal Produksi:</label>
                                    <input type="date" name="date" id="" class="form-control"
                                        oninput="this.value = this.value.toUpperCase()">
                                </div>
                                <div class="form-group">
                                    <label for="seam" class="col-form-label">Run Mesin:</label>
                                    <input type="number" name="run_mc" id="" class="form-control"
                                        oninput="this.value = this.value.toUpperCase()">
                                </div>

                                <button type="submit" class="btn btn-info btn-block w-100"> Simpan</button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Trigger import modal when import button is clicked
        $('.import-btn').click(function() {

            $('#importModal').modal('show'); // Show the modal
        });

        new DataTable('#example', {
            layout: {
                top1Start: {
                    buttons: [{
                        extend: 'excel',
                        text: 'Export To Excel',
                        className: 'btn btn-success btn-sm'
                    }]
                },
                top1End: {
                    buttons: [{
                        text: 'Import Data',
                        className: 'btn btn-success btn-sm btn_import'
                    }]
                }
            }
        });

        $('.btn_import').click(async (e) => {
            e.preventDefault();
            $('#importInput')[0].click();
        })
    });

    $('#importInput').change(() => {
        $('#form-import').submit();
    });

    $('#productType').change(() => {
        $.ajax({
            url: 'getInitialByModel',
            method: 'post',
            dataType: 'json',
            data: {
                id_order: $('#productType').val()
            },
            success: (data) => {
                $('#id_inisial').html('')
                var html = ""
                data.map((item) => {
                    html += `<option value="${item.id_inisial}">${item.inisial}</option>`
                })
                $('#id_inisial').html(html)
            }
        })
    })
</script>
<?php $this->endSection(); ?>