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
                            Skill Mapping
                            <h4 class="font-weight-bolder">
                                Report Summary Rosso
                            </h4>
                            <h5>
                                <?= $periode['nama_batch'] ?> Periode <?= $periode['nama_periode'] ?>
                            </h5>
                            <h6>
                                <?= $periode['start_date'] ?> s/d <?= $periode['end_date'] ?>
                            </h6>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('Monitoring/reportSummaryRosso') ?>" class="btn bg-gradient-secondary btn-sm">
                                        <i class="fas fa-solid fa-arrow-left text-sm opacity-10"></i>
                                        Kembali
                                </a>
                                <div> &nbsp;</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">

                <div class="card-body">


                    <div class="table-responsive">
                        <table id="rossoTable" class="table table-striped table-hover table-bordered w-100">
                            <thead>
                                <th>No</th>
                                <th>Kode Kartu</th>
                                <th>Nama Karyawan</th>
                                <th>L/P</th>
                                <th>TGL Masuk Kerja</th>
                                <th>Bagian</th>
                                <th>PERBAIKAN (PCS)</th>
                                <th>PRODUKSI (PCS)</th>
                            </thead>
                            <tbody>
                                <?php if (!empty($SummaryRosso)) : ?>
                                    <?php $no = 1;
                                    foreach ($SummaryRosso as $r) : ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $r['kode_kartu'] ?></td>
                                            <td><?= $r['nama_karyawan'] ?></td>
                                            <td><?= $r['jenis_kelamin'] ?></td>
                                            <td><?= $r['tgl_masuk'] ?></td>
                                            <td><?= $r['nama_bagian'] ?></td>
                                            <td><?= number_format($r['avg_qty_bs'], 2) ?></td>
                                            <td><?= number_format($r['avg_qty_prod_rosso'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
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