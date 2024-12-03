<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>',
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
                    html: '<?= session()->getFlashdata('error') ?>',
                });
            });
        </script>
    <?php endif; ?>
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="font-weight-bolder mb-0">
                                <a href="" # class="btn bg-gradient-info">
                                    <!-- icon data mesin -->
                                    <i class="fas fa-cogs text-lg opacity-10" aria-hidden="true"></i>
                                </a>
                                Data Rosso
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <!-- button template excel-->
                                <a href="<?= base_url('Monitoring/downloadTemplateRosso') ?>" class="btn bg-gradient-success btn-sm me-2">
                                    <!-- icon download-->
                                    <i class="fas fa-download text-lg opacity-10" aria-hidden="true"></i>
                                    Template Excel
                                </a>
                                <a href="#" class="btn bg-gradient-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#addRosso">
                                    <!-- icon tambah-->
                                    <i class="fas fa-plus text-lg opacity-10" aria-hidden="true"></i>
                                    Rosso
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
                    <h4 class="card-title">
                        Import Rosso
                    </h4>
                    <!-- form import  data absen -->
                    <form action="<?= base_url('Monitoring/rossoStoreImport') ?>" method="post"
                        enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="periode">Periode</label>
                                    <select class="form-select" name="periode" id="periode" required>
                                        <option value="">Pilih Periode</option>
                                        <?php foreach ($periode as $p) : ?>
                                            <option value="<?= $p['id_periode'] ?>">Periode <?= $p['nama_periode'] ?> (<?= $p['start_date'] ?> - <?= $p['end_date'] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="upload-container">
                            <div class="upload-area" id="upload-area">
                                <i class="fas fa-cloud-upload-alt fa-2x"></i>
                                <p>Drag & drop any file here</p>
                                <span>or <label for="file-upload" class="browse-link">browse file</label> from
                                    device</span>
                                <input type="file" id="file-upload" class="file-input" name="file" hidden required>
                            </div>
                            <button type="submit" class="upload-button w-100 mt-3">
                                <i class="fas fa-upload"></i> Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        Tabel Rosso
                    </h4>
                    <div class="table-responsive">
                        <table id="rossoTable" class="table table-striped table-hover table-bordered w-100">
                            <thead>
                                <th>No</th>
                                <th>Kode Kartu</th>
                                <th>Nama Karyawan</th>
                                <!-- <th>Shift</th> -->
                                <th>Tgl Prod Rossso</th>
                                <th>Qty Prod Rosso</th>
                                <th>Qty Bs</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                <?php if (!empty($summaryRosso)) : ?>
                                    <?php foreach ($summaryRosso as $sr) : ?>
                                        <tr>
                                            <td><?= $sr['id_sr'] ?></td>
                                            <td><?= $sr['kode_kartu'] ?></td>
                                            <td><?= $sr['nama_karyawan'] ?></td>
                                            <!-- <td><?= $sr['shift'] ?></td> -->
                                            <td><?= $sr['tgl_prod_rosso'] ?></td>
                                            <td><?= $sr['qty_prod_rosso'] ?></td>
                                            <td><?= $sr['qty_bs'] ?></td>
                                            <td>
                                                <a class="btn btn-warning edit-btn"
                                                    data-id="<?= $sr['id_sr'] ?>"
                                                    data-idKar="<?= $sr['id_karyawan'] ?>"
                                                    data-idPeriode="<?= $sr['id_periode'] ?>"
                                                    data-tglProdRosso="<?= $sr['tgl_prod_rosso'] ?>"
                                                    data-qtyProdRosso="<?= $sr['qty_prod_rosso'] ?>"
                                                    data-qtyBs="<?= $sr['qty_bs'] ?>"
                                                    data-bs-toggle="modal" data-bs-target="#editUser">
                                                    <!-- icon edit -->
                                                    <i class="fas fa-edit text-lg opacity-10" aria-hidden="true"></i>
                                                </a>
                                                <button class="btn bg-gradient-danger btn-sm"
                                                    onclick="confirmDelete('<?= $sr['id_sr'] ?>')">
                                                    <!-- icon hapus -->
                                                    <i class="fas fa-trash text-lg opacity-10" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No Data found</td>
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
<!-- Modal -->
<div class="modal fade" id="addRosso" tabindex="-1" aria-labelledby="addRossoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRossoLabel">Tambah Data Rosso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <form action="<?= base_url('Monitoring/rossoStore') ?>" method="post">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="id_periode" class="form-label">Periode</label>
                        <select class="form-select" name="id_periode" id="id_periode" required>
                            <option value="">Pilih Periode</option>
                            <?php foreach ($periode as $p) : ?>
                                <option value="<?= $p['id_periode'] ?>">Periode <?= $p['nama_periode'] ?> (<?= $p['start_date'] ?> - <?= $p['end_date'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_karyawan" class="form-label">Kode Kartu</label>
                        <select class="form-select" name="id_karyawan" id="id_karyawan" required>
                            <option value="">Pilih Kode Kartu</option>
                            <?php foreach ($karyawan as $k) : ?>
                                <option value="<?= $k['id_karyawan'] ?>"><?= $k['kode_kartu'] ?> - <?= $k['nama_karyawan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tgl_prod_rosso" class="form-label">Tanggal Produksi Rosso</label>
                        <input type="date" class="form-control" id="tgl_prod_rosso" name="tgl_prod_rosso" required>
                    </div>
                    <div class="mb-3">
                        <label for="qty_prod_rosso" class="form-label">Qty Produksi Rosso</label>
                        <input type="number" class="form-control" id="qty_prod_rosso" name="qty_prod_rosso" required>
                    </div>
                    <div class="mb-3">
                        <label for="qty_bs" class="form-label">Qty BS</label>
                        <input type="number" class="form-control" id="qty_bs" name="qty_bs" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-gradient-info">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Edit-->
<div class="modal fade  bd-example-modal-lg" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="ModalEdit" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserLabel">Edit Data Rosso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <form action="" method="post">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="id_periode" class="form-label">Periode</label>
                        <select class="form-select" name="id_periode" id="id_periode" required>
                            <option value="">Pilih Periode</option>
                            <?php foreach ($periode as $p) : ?>
                                <option value="<?= $p['id_periode'] ?>">Periode <?= $p['nama_periode'] ?> (<?= $p['start_date'] ?> - <?= $p['end_date'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_karyawan" class="form-label">Kode Kartu</label>
                        <select class="form-select" name="id_karyawan" id="id_karyawan" required>
                            <option value="">Pilih Kode Kartu</option>
                            <?php foreach ($karyawan as $k) : ?>
                                <option value="<?= $k['id_karyawan'] ?>"><?= $k['kode_kartu'] ?> - <?= $k['nama_karyawan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tgl_prod_rosso" class="form-label ">Tanggal Produksi Rosso</label>
                        <input type="date" class="form-control" id="tgl_prod_rosso" name="tgl_prod_rosso" value="" required>
                    </div>
                    <div class="mb-3">
                        <label for="qty_prod_rosso" class="form-label">Qty Produksi Rosso</label>
                        <input type="number" class="form-control" id="qty_prod_rosso" name="qty_prod_rosso" value="" required>
                    </div>
                    <div class="mb-3">
                        <label for="qty_bs" class="form-label">Qty BS</label>
                        <input type="number" class="form-control" id="qty_bs" name="qty_bs" value="" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-gradient-info">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tambahkan di bagian <head> -->
<link href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const kodeKartu = new Choices('#id_karyawan', {
            searchPlaceholderValue: 'Cari kode kartu...',
            shouldSort: false
        });
    });
</script>


<script>
    // button close alert
    const closeBtn = document.querySelectorAll('.btn-close');
    closeBtn.forEach(btn => {
        btn.addEventListener('click', () => {
            btn.parentElement.style.display = 'none';
        });
    });
</script>
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
                window.location.href = "<?= base_url('Monitoring/rossoDelete/') ?>" + id;
            }
        })
    }
</script>

<script>
    $(document).ready(function() {
        $('#rossoTable').DataTable({});
    });
</script>

<script>
    const fileInput = document.getElementById('file-upload');
    const uploadArea = document.getElementById('upload-area');

    fileInput.addEventListener('change', (event) => {
        const fileName = event.target.files[0] ? event.target.files[0].name : "No file selected";
        uploadArea.querySelector('p').textContent = `Selected File: ${fileName}`;
    });

    uploadArea.addEventListener('dragover', (event) => {
        event.preventDefault();
        uploadArea.style.backgroundColor = "#e6f5ff";
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.style.backgroundColor = "#ffffff";
    });

    uploadArea.addEventListener('drop', (event) => {
        event.preventDefault();
        fileInput.files = event.dataTransfer.files;
        const fileName = event.dataTransfer.files[0] ? event.dataTransfer.files[0].name : "No file selected";
        uploadArea.querySelector('p').textContent = `Selected File: ${fileName}`;
    });

    $('.edit-btn').click(function() {
        var id = $(this).data('id');
        var idKar = $(this).data('idkar');
        var idPeriode = $(this).data('idperiode');
        var tglProdRosso = $(this).data('tglprodrosso');
        var qtyProdRosso = $(this).data('qtyprodrosso');
        var qtyBs = $(this).data('qtybs');


        // console.log(id, idKar, idPeriode, tglProdRosso, qtyProdRosso, qtyBs);
        $('#ModalEdit').find('form').attr('action', '<?= base_url('Monitoring/rossoUpdate') ?>/' + id);
        $('#ModalEdit').find('#id_karyawan').val(idKar);
        $('#ModalEdit').find('#id_periode').val(idPeriode);
        $('#ModalEdit').find('#tgl_prod_rosso').val(tglProdRosso);
        $('#ModalEdit').find('#qty_prod_rosso').val(qtyProdRosso);
        $('#ModalEdit').find('#qty_bs').val(qtyBs);
        $('#ModalEdit').modal('show'); // Show the modal
    });
</script>




<?php $this->endSection(); ?>