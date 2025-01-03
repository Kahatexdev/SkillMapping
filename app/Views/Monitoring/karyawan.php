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
                                <a href="#" class="btn bg-gradient-info">
                                    <!-- icon data karyawan -->
                                    <i class="fas fa-user-tie text-lg opacity-10" aria-hidden="true"></i>
                                </a>
                                Data Karyawan
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('Monitoring/exportKaryawan/') ?>"
                                    class="btn bg-gradient-primary me-2">
                                    <!-- icon download -->
                                    <i class="fas fa-file-excel text-lg opacity-10" aria-hidden="true"></i>
                                    Export Excel
                                </a>
                                <a href="<?= base_url('Monitoring/downloadTemplateKaryawan') ?>"
                                    class="btn bg-gradient-success me-2">
                                    <!-- icon download -->
                                    <i class="fas fa-download text-lg opacity-10" aria-hidden="true"></i>
                                    Template Excel
                                </a>
                                <a href="<?= base_url('Monitoring/karyawanCreate') ?>"
                                    class="btn bg-gradient-info add-btn" data-bs-toggle="modal" data-bs-target="#addKaryawan">
                                    <!-- icon tambah karyawan-->
                                    <i class="fas fa-user-plus text-lg opacity-10" aria-hidden="true"></i>
                                    Data Karyawan
                                </a>
                                <div> &nbsp;</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade  bd-example-modal-lg" id="addKaryawan" tabindex="-1" role="dialog" aria-labelledby="addKaryawan" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Karyawan</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= base_url($role . '/karyawanStore'); ?>" method="post">
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    <div class="form-group mb-2">
                                        <label for="kode_kartu">Kode Kartu</label>
                                        <input type="text" class="form-control" name="kode_kartu" id="kode_kartu" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="nama_karyawan">Nama Karyawan</label>
                                        <input type="text" class="form-control" name="nama_karyawan" id="nama_karyawan" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="shift">Shift</label>
                                        <select name="shift" id="shift" class="form-control" required>
                                            <option value="">Pilih Shift</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="Non Shift">Non Shift</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="jenis_kelamin">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="libur">Libur</label>
                                        <input type="text" class="form-control" name="libur" id="libur" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="libur_tambahan">Libur Tambahan</label>
                                        <input type="text" class="form-control" name="libur_tambahan" id="libur_tambahan" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="warna_baju">Warna Baju</label>
                                        <select name="warna_baju" id="warna_baju" class="form-control" required>
                                            <option value="">Pilih Warna Baju</option>
                                            <option value="Putih">Putih</option>
                                            <option value="BIRU">BIRU</option>
                                            <option value="Kuning">Kuning</option>
                                            <option value="Pink">Pink</option>
                                            <option value="Coklat">Coklat</option>
                                            <option value="Hijau">Hijau</option>
                                            <option value="Hitam">Hitam</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="status_baju">Status Baju</label>
                                        <select name="status_baju" id="status_baju" class="form-control" required>
                                            <option value="">Pilih Status Baju</option>
                                            <option value="Harian">Harian</option>
                                            <option value="Training">Training</option>
                                            <option value="Magang">Magang</option>
                                            <option value="KARYAWAN">KARYAWAN</option>
                                            <option value="Staff">Staff</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="tgl_lahir">Tanggal Lahir</label>
                                        <input type="date" class="form-control" name="tgl_lahir" id="tgl_lahir" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="tgl_masuk">Tanggal Masuk</label>
                                        <input type="date" class="form-control" name="tgl_masuk" id="tgl_masuk" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="bagian">Bagian</label>
                                        <select name="bagian" id="bagian" class="form-control" required>
                                            <option value="">Pilih Bagian</option>
                                            <?php foreach ($bagian as $row) : ?>
                                                <option value="<?= $row['id_bagian'] ?>">
                                                    <?= $row['nama_bagian'] . ' - ' . $row['area_utama'] . ' - ' . $row['area'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="status_aktif">Status Aktif</label>
                                        <select name="status_aktif" id="status_aktif" class="form-control" required>
                                            <option value="">Pilih Status Aktif</option>
                                            <option value="Aktif">Aktif</option>
                                            <option value="Tidak Aktif">Tidak Aktif</option>
                                        </select>
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
    </div>
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-2 mt-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        Import Data Karyawan
                    </h5>
                    <!-- form import  data karyawan -->
                    <form action="<?= base_url('Monitoring/karyawanStoreImport') ?>" method="post"
                        enctype="multipart/form-data">
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
    <div class="row mt-1">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        Tabel Data Karyawan
                    </h4>
                    <div class="table-responsive">
                        <table id="karyawanTable" class="table table-striped table-hover table-bordered w-100">
                            <thead>
                                <th>No</th>
                                <th>Kode Kartu</th>
                                <th>Nama Karyawan</th>
                                <th>Shift</th>
                                <th>Warna Baju</th>
                                <th>Bagian</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                <?php if (!empty($karyawan)) : ?>
                                    <?php foreach ($karyawan as $karyawan) : ?>
                                        <tr>
                                            <td><?= $karyawan['id_karyawan'] ?></td>
                                            <td><?= $karyawan['kode_kartu'] ?></td>
                                            <td><?= $karyawan['nama_karyawan'] ?></td>
                                            <td><?= $karyawan['shift'] ?></td>

                                            <td><?= $karyawan['warna_baju'] ?></td>

                                            <td><?= $karyawan['nama_bagian'] . ' - ' . $karyawan['area_utama'] . ' - ' . $karyawan['area'] ?></td>
                                            <input type="hidden" name="id_bagian" value="<?= $karyawan['id_bagian'] ?>">
                                            <td><?= $karyawan['status_aktif'] ?></td>
                                            <td>
                                                <a class="btn btn-warning edit-btn"
                                                    data-id="<?= $karyawan['id_karyawan'] ?> "
                                                    data-kode_kartu="<?= $karyawan['kode_kartu'] ?>"
                                                    data-nama="<?= $karyawan['nama_karyawan'] ?>"
                                                    data-shift="<?= $karyawan['shift'] ?>"
                                                    data-jenis_kelamin="<?= $karyawan['jenis_kelamin'] ?>"
                                                    data-areaUtama="<?= $karyawan['area_utama'] ?>"
                                                    data-area="<?= $karyawan['area'] ?>"
                                                    data-libur="<?= $karyawan['libur'] ?>"
                                                    data-libur_tambahan="<?= $karyawan['libur_tambahan'] ?>"
                                                    data-warna_baju="<?= $karyawan['warna_baju'] ?>"
                                                    data-status_baju="<?= $karyawan['status_baju'] ?>"
                                                    data-tgl_lahir="<?= $karyawan['tgl_lahir'] ?>"
                                                    data-tgl_masuk="<?= $karyawan['tgl_masuk'] ?>"
                                                    data-nama_bagian="<?= $karyawan['id_bagian'] ?>"
                                                    data-status_aktif="<?= $karyawan['status_aktif'] ?>"
                                                    data-bs-toggle="modal" data-bs-target="#editUser">
                                                    <!-- icon edit -->
                                                    <i class="fas fa-edit text-lg opacity-10" aria-hidden="true"></i>
                                                </a>
                                                <button class="btn bg-gradient-danger btn-sm"
                                                    onclick="confirmDelete('<?= $karyawan['id_karyawan'] ?>')">
                                                    <!-- icon hapus -->
                                                    <i class="fas fa-trash text-lg opacity-10" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="14" class="text-center">No Karyawan found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade  bd-example-modal-lg" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="ModalEdit" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Data Karyawan</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">

                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <div class="form-group mb-2">
                                    <label for="kode_kartu">Kode Kartu</label>
                                    <input type="text" class="form-control" name="kode_kartu" id="kode_kartu"
                                        value="" required>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="nama_karyawan">Nama Karyawan</label>
                                    <input type="text" class="form-control" name="nama_karyawan" id="nama_karyawan"
                                        value="" required>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="shift">Shift</label>
                                    <select name="shift" id="shift" class="form-control" required>
                                        <option value="">Pilih Shift</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="Non Shift">Non
                                            Shift</option>
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="jenis_kelamin">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L">Laki-laki
                                        </option>
                                        <option value="P">Perempuan
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="libur">Libur</label>
                                    <input type="text" class="form-control" name="libur" id="libur"
                                        value="" required>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="libur_tambahan">Libur Tambahan</label>
                                    <input type="text" class="form-control" name="libur_tambahan" id="libur_tambahan"
                                        value="" required>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="warna_baju">Warna Baju</label>
                                    <select name="warna_baju" id="warna_baju" class="form-control" required>
                                        <option value="">Pilih Warna Baju</option>
                                        <option value="Merah">Merah
                                        </option>
                                        <option value="BIRU">BIRU
                                        </option>
                                        <option value="Hijau">Hijau
                                        </option>
                                        <option value="Kuning">
                                            Kuning
                                        </option>
                                        <option value="Putih">Putih
                                        </option>
                                        <option value="Hitam">Hitam
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="status_baju">Status Baju</label>
                                    <select name="status_baju" id="status_baju" class="form-control" required>
                                        <option value="">Pilih Status Baju</option>
                                        <option value="Harian">
                                            Harian
                                        </option>
                                        <option value="Training">
                                            Training</option>
                                        <option value="Magang">
                                            Magang
                                        </option>
                                        <option value="KARYAWAN">
                                            KARYAWAN</option>
                                        <option value="Staff">Staff
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="tgl_lahir">Tanggal Lahir</label>
                                    <input type="date" class="form-control" name="tgl_lahir" id="tgl_lahir"
                                        value="" required>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="tgl_masuk">Tanggal Masuk</label>
                                    <input type="date" class="form-control" name="tgl_masuk" id="tgl_masuk"
                                        value="" required>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="bagian">Bagian</label>
                                    <select name="bagian" id="bagian" class="form-control" required>
                                        <option value="">Pilih Bagian</option>
                                        <?php foreach ($bagian as $row) : ?>
                                            <option value="<?= $row['id_bagian'] ?>">
                                                <?= $row['nama_bagian'] . ' - ' . $row['area_utama'] . ' - ' . $row['area'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="status_aktif">Status Aktif</label>
                                    <select name="status_aktif" id="status_aktif" class="form-control" required>
                                        <option value="">Pilih Status Aktif</option>
                                        <option value="Aktif">
                                            Aktif
                                        </option>
                                        <option value="Tidak Aktif">Tidak Aktif
                                        </option>
                                    </select>
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
                window.location.href = "<?= base_url('Monitoring/karyawanDelete/') ?>" + id;
            }
        })
    }
</script>
<script>
    $(document).ready(function() {
        // Initialize DataTable with export options
        $('#karyawanTable').DataTable({});

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
        var nama = $(this).data('nama');
        var shift = $(this).data('shift');
        var kode_kartu = $(this).data('kode_kartu');
        var jenis_kelamin = $(this).data('jenis_kelamin');
        var areaUtama = $(this).data('areaUtama');
        var area = $(this).data('area');
        var libur = $(this).data('libur');
        var libur_tambahan = $(this).data('libur_tambahan');
        var warna_baju = $(this).data('warna_baju');
        var status_baju = $(this).data('status_baju');
        var tgl_lahir = $(this).data('tgl_lahir');
        var tgl_masuk = $(this).data('tgl_masuk');
        var nama_bagian = $(this).data('nama_bagian');
        var status_aktif = $(this).data('status_aktif');

        $('#ModalEdit').find('form').attr('action', '<?= base_url('Monitoring/karyawanUpdate/') ?>' + id);
        $('#ModalEdit').find('#kode_kartu').val(kode_kartu);
        $('#ModalEdit').find('#nama_karyawan').val(nama);
        $('#ModalEdit').find('#shift').val(shift);
        $('#ModalEdit').find('#jenis_kelamin').val(jenis_kelamin);
        $('#ModalEdit').find('#areaUtama').val(areaUtama);
        $('#ModalEdit').find('#area').val(area);
        $('#ModalEdit').find('#libur').val(libur);
        $('#ModalEdit').find('#libur_tambahan').val(libur_tambahan);
        $('#ModalEdit').find('#warna_baju').val(warna_baju);
        $('#ModalEdit').find('#status_baju').val(status_baju);
        $('#ModalEdit').find('#tgl_lahir').val(tgl_lahir);
        $('#ModalEdit').find('#tgl_masuk').val(tgl_masuk);
        $('#ModalEdit').find('#bagian').val(nama_bagian);
        $('#ModalEdit').find('#status_aktif').val(status_aktif);
        $('#ModalEdit').modal('show'); // Show the modal
    });
</script>

<?php $this->endSection(); ?>