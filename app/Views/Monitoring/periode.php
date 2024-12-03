<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid">
    <div class="row my-2">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-2">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="font-weight-bolder mb-0">
                                <a href="" # class="btn bg-gradient-info">
                                    <!-- icon data periode -->
                                    <i class="fas fa-calendar text-lg opacity-10 me-1" aria-hidden="true"> </i>
                                </a>
                                Data Periode
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href="" class="btn bg-gradient-info add-btn" data-bs-toggle="modal" data-bs-target="#addPeriode">
                                    <!-- icon tambah periode-->
                                    <i class="fas fa-plus text-lg opacity-10 me-1" aria-hidden="true"></i>
                                    Tambah Periode
                                </a>
                                <div> &nbsp;</div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        Tabel Data periode
                    </h5>
                    <div class="table-responsive">
                        <table id="periodeTable" class="table table-striped table-hover table-bordered w-100">
                            <thead>
                                <th>No</th>
                                <th>Nama periode</th>
                                <th>Nama Batch</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                <?php if (!empty($periode)) : ?>
                                    <?php foreach ($periode as $periode) : ?>
                                        <tr>
                                            <td><?= $periode['id_periode'] ?></td>
                                            <td><?= $periode['nama_periode'] ?></td>
                                            <td><?= $periode['nama_batch'] ?></td>
                                            <td><?= $periode['start_date'] ?></td>
                                            <td><?= $periode['end_date'] ?></td>
                                            <td>
                                                <a href="#"
                                                    class="btn btn-warning edit-btn" data-id="<?= $periode['id_periode'] ?>"
                                                    data-nama="<?= $periode['nama_periode'] ?>" data-idbatch="<?= $periode['nama_batch'] ?>"
                                                    data-startdate="<?= $periode['start_date'] ?>" data-enddate="<?= $periode['end_date'] ?>"
                                                    data-bs-toggle=" modal" data-bs-target="#editUser">
                                                    <i class=" fas fa-edit text-lg opacity-10" aria-hidden="true"></i>
                                                </a>
                                                <button onclick="confirmDelete(<?= $periode['id_periode'] ?>)"
                                                    class="btn bg-gradient-danger btn-sm">
                                                    <i class="fas fa-trash text-lg opacity-10" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak Ada Data Periode</td>
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

<!-- Modal Tambah -->
<div class="modal fade  bd-example-modal-lg" id="addPeriode" tabindex="-1" role="dialog" aria-labelledby="addPeriode" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Periode</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url($role . '/periodeStore'); ?>" method="post">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12">
                            <div class="form-group mb-2">
                                <label for="nama_periode">Nama Periode</label>
                                <select class="form-control" name="nama_periode" id="nama_periode" required>
                                    <option value="">Pilih Periode</option>
                                    <option value="Awal">Awal</option>
                                    <option value="Tengah">Tengah</option>
                                    <option value="Akhir">Akhir</option>
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label for="area_utama">Batch</label>
                                <select class="form-control" name="nama_batch" id="nama_batch" required>
                                    <option value="">Pilih Batch</option>
                                    <?php foreach ($batch as $addbatch) : ?>
                                        <option value="<?= $addbatch['id_batch'] ?>"><?= $addbatch['nama_batch'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label for="area">Start Date</label>
                                <input type="date" class="form-control" name="start_date" id="start_date">
                            </div>
                            <div class="form-group mb-2">
                                <label for="keterangan">End Date</label>
                                <input type="date" class="form-control" name="end_date" id="end_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade  bd-example-modal-lg" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="ModalEdit" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Periode</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12">
                            <div class="form-group mb-2">
                                <label for="nama_periode">Nama Periode</label>
                                <select class="form-control" name="nama_periode" id="nama_periode" required>
                                    <option value="">Pilih Periode</option>
                                    <option value="Awal">Awal</option>
                                    <option value="Tengah">Tengah</option>
                                    <option value="Akhir">Akhir</option>
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label for="area_utama">Batch</label>
                                <select class="form-control" name="nama_batch" id="nama_batch" required>
                                    <option value="">Pilih Batch</option>
                                    <?php foreach ($batch as $batch) : ?>
                                        <option value="<?= $batch['id_batch'] ?>"><?= $batch['nama_batch'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label for="area">Start Date</label>
                                <input type="date" class="form-control" name="start_date" id="start_date">
                            </div>
                            <div class="form-group mb-2">
                                <label for="keterangan">End Date</label>
                                <input type="date" class="form-control" name="end_date" id="end_date" required>
                            </div>
                        </div>
                    </div>
                    <!-- Tombol Aksi -->
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('Monitoring/periodeDelete/') ?>" + id;
            }
        })
    }
</script>
<script>
    $(document).ready(function() {
        // Initialize DataTable with export options
        $('#periodeTable').DataTable({});

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

    $('.edit-btn').click(function() {
        var id = $(this).data('id');
        var namaPeriode = $(this).data('nama');
        var idBatch = $(this).data('idbatch');
        var startDate = $(this).data('startdate');
        var endDate = $(this).data('enddate');

        $('#ModalEdit').find('form').attr('action', '<?= base_url('Monitoring/periodeUpdate/') ?>' + id);
        $('#ModalEdit').find('#nama_periode').val(namaPeriode);
        $('#ModalEdit').find('#id_batch').val(idBatch);
        $('#ModalEdit').find('#start_date').val(startDate);
        $('#ModalEdit').find('#end_date').val(endDate);
        $('#ModalEdit').modal('show'); // Show the modal
    });
</script>

<?php $this->endSection(); ?>