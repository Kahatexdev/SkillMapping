<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        Input Pemakaian Jarum
                    </h4>
                    <!-- Form Input Summary Jarum -->
                    <form action="<?= base_url('Monitoring/jarumStoreInput') ?>" method="post"
                        enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="id_batch">Pilih Tanggal Input</label>
                                    <input type="date" class="form-control" id="tgl_input" name="tgl_input" required>
                                </div>
                            </div>
                        </div>

                        <div class="div" id="inputRows">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Area</label>
                                        <select class="form-select" name="area[]" id="area" required>
                                            <option value="">Pilih Area</option>
                                            <?php foreach ($getArea as $key => $ar) : ?>
                                                <option value="<?= $ar['area'] ?>"><?= $ar['area'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="">Montir</label>
                                    <select class="form-control" id="id_karyawan" name="id_karyawan[]" required>
                                        <option value="">Pilih Montir</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="">Pemakaian Jarum</label>
                                    <input type="number" class="form-control" name="used_needle[]" id="used_needle" required>
                                </div>
                                <div class="col-md-3 text-center">
                                    <label for="">Aksi</label>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" class="btn btn-info" id="addRow">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <!-- <button class="btn btn-outline-danger remove-tab" type="button">
                                        <i class="fas fa-trash"></i>
                                    </button> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-center">
                                    <button type="submit" class="btn bg-gradient-info mt-3 w-100">Simpan</button>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Human Resource System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Summary Jarum Per Area
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <a href="<?= base_url('Monitoring/downloadTemplateJarum') ?>"
                                class="btn bg-gradient-success me-2">
                                <!-- icon download -->
                                <i class="fas fa-download text-lg opacity-10" aria-hidden="true"></i>
                                Template Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <?php foreach ($getArea as $key => $ar) : ?>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
                <a href="<?= base_url($role . '/dataJarum/' . $ar['area']) ?>">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold"><?= $ar['area'] ?></p>
                                        <h5 class="font-weight-bolder mb-0">
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                        <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach ?>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTable with export options
        $('#table_report_batch').DataTable({});

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
    $(document).ready(function() {
        $('#area').change(function() {
            var area = $(this).val();

            $('#id_karyawan').html('<option value="">Loading...</option>');

            if (area !== "") {
                $.ajax({
                    url: "<?= base_url($role . '/getMontirByArea') ?>",
                    type: "POST",
                    data: {
                        area: area
                    },
                    dataType: "json",
                    success: function(response) {
                        var options = '<option value="">Pilih Montir</option>';
                        $.each(response, function(index, montir) {
                            options += '<option value="' + montir.id_karyawan + '">' + montir.nama_karyawan + ' | ' + montir.kode_kartu + '</option>';
                        });
                        $('#id_karyawan').html(options);
                    },
                    error: function() {
                        $('#id_karyawan').html('<option value="">Error</option>');
                    }
                });
            } else {
                $('#id_karyawan').html('<option value="">Pilih Montir</option>');
            }
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const addRowBtn = document.getElementById("addRow");
        const inputRowsContainer = document.getElementById("inputRows");
        const form = document.querySelector("form");

        addRowBtn.addEventListener("click", function() {
            const row = document.createElement("div");
            row.classList.add("row", "mt-2");

            row.innerHTML = `
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Area</label>
                    <select class="form-select area-dropdown" name="area[]" required>
                        <option value="">Pilih Area</option>
                        ${getAreaOptions()}
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <label for="">Montir</label>
                <select class="form-control montir-dropdown" name="id_karyawan[]" required>
                    <option value="">Pilih Montir</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="">Pemakaian Jarum</label>
                <input type="number" class="form-control" name="used_needle[]" required>
            </div>
            <div class="col-md-3 text-center">
                <label for="">Aksi</label>
                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-outline-danger remove-row" type="button">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;

            inputRowsContainer.appendChild(row);

            // Tambahkan event listener untuk hapus row
            row.querySelector(".remove-row").addEventListener("click", function() {
                row.remove();
            });
        });

        // Delegasi event untuk menangani perubahan area di semua dropdown area
        document.addEventListener("change", function(event) {
            if (event.target.classList.contains("area-dropdown")) {
                let area = event.target.value;
                let montirDropdown = event.target.closest(".row").querySelector(".montir-dropdown");

                montirDropdown.innerHTML = '<option value="">Loading...</option>';

                if (area !== "") {
                    $.ajax({
                        url: "<?= base_url($role . '/getMontirByArea') ?>",
                        type: "POST",
                        data: {
                            area: area
                        },
                        dataType: "json",
                        success: function(response) {
                            let options = '<option value="">Pilih Montir</option>';
                            response.forEach(montir => {
                                options += `<option value="${montir.id_karyawan}">${montir.nama_karyawan} | ${montir.kode_kartu}</option>`;
                            });
                            montirDropdown.innerHTML = options;
                        },
                        error: function() {
                            montirDropdown.innerHTML = '<option value="">Error</option>';
                        }
                    });
                } else {
                    montirDropdown.innerHTML = '<option value="">Pilih Montir</option>';
                }
            }
        });

        // Fungsi untuk mendapatkan daftar opsi area yang tersedia
        function getAreaOptions() {
            let options = "";
            <?php foreach ($getArea as $ar) : ?>
                options += `<option value="<?= $ar['area'] ?>"><?= $ar['area'] ?></option>`;
            <?php endforeach ?>
            return options;
        }
    });
</script>
<?php $this->endSection(); ?>