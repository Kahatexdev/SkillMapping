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
                                <a href="<?= base_url('monitoring/downloadTemplateRosso') ?>" class="btn bg-gradient-success btn-sm me-2">
                                    <!-- icon download-->
                                    <i class="fas fa-download text-lg opacity-10" aria-hidden="true"></i>
                                    Template Excel
                                </a>
                                <a href="<?= base_url('monitoring/rossoCreate') ?>" class="btn bg-gradient-info btn-sm me-2">
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
                <div class="card-header">
                    <h4 class="card-title">
                        Import Rosso
                    </h4>
                </div>
                <div class="card-body">
                    <!-- form import  data absen -->
                    <form action="<?= base_url('monitoring/rossoStoreImport') ?>" method="post"
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
                <div class="card-header">
                    <h4 class="card-title">
                        Table Rosso
                    </h4>
                </div>
                <div class="card-body">
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
                                                <a class="btn bg-gradient-warning btn-sm"
                                                    href="<?= base_url('monitoring/rossoEdit/' . $sr['id_sr']) ?>">
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
                window.location.href = "<?= base_url('monitoring/rossoDelete/') ?>" + id;
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
</script>




<?php $this->endSection(); ?>