<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>
        Skill Mapping
    </title>
    <!--     Fonts and icons     -->
    <link href="<?= base_url('assets/css/open_sans_family.css') ?>" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="<?= base_url('assets/css/nucleo-icons.css') ?>" rel=" stylesheet" />
    <link href="<?= base_url('assets/css/nucleo-svg.css') ?>" rel=" stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="<?= base_url('assets/fa/js/fontawesome.min.js') ?>"></script>
    <link href="<?= base_url('assets/fa/css/all.min.css') ?>" rel=" stylesheet" />
    <!-- CSS Files -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link id="pagestyle" href="<?= base_url('assets/css/soft-ui-dashboard.css?v=1.0.7') ?>" rel="stylesheet" />
    <!--  -->
    <script src="<?= base_url('assets/js/jquery/jquery-3.7.1.min.js') ?>" crossorigin="anonymous"></script>
    <link href="<?= base_url('assets/css/dataTables.dataTables.css') ?>" rel="stylesheet">
    <script src="<?= base_url('assets/js/dataTables.min.js') ?>"></script>
    <link href="<?= base_url('assets/css/select2.min.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/sweetalert2.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.4/css/buttons.dataTables.min.css">
    <style>
        .upload-container {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e3e6f0;
        }

        .upload-area {
            border: 2px dashed #007bff;
            padding: 30px;
            border-radius: 10px;
            background-color: #ffffff;
            color: #007bff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .upload-area:hover {
            background-color: #e9f4ff;
        }

        .upload-area i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .upload-area p {
            font-size: 16px;
            font-weight: bold;
        }

        .browse-link {
            color: #007bff;
            text-decoration: underline;
            cursor: pointer;
        }

        .upload-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .upload-button:hover {
            background-color: #0056b3;
        }
    </style>


</head>

<body class="g-sidenav-show bg-gray-100 bg-opacity-50">

    <?php include('sidebar.php'); ?>
    <?php include('navbar.php'); ?>

    <!--   Core JS Files   -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url('assets/js/select2.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/core/popper.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/core/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/plugins/perfect-scrollbar.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/plugins/smooth-scrollbar.min.js') ?>"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="<?= base_url('assets/js/buttons.js') ?>"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="<?= base_url('assets/js/soft-ui-dashboard.min.js?v=1.0.7') ?>"></script>
</body>

</html>