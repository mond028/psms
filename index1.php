<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location:./index.php");
    exit;
}
require_once('DBConnection.php');
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
if($_SESSION['type'] != 1 && in_array($page,array('maintenance','products','stocks'))){
    header("Location:./");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucwords(str_replace('_',' ',$page)) ?> | Petrol Station</title>
    <link rel="stylesheet" href="./Font-Awesome-master/css/all.min.css">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/popper.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="./DataTables/datatables.min.css">
    <script src="./DataTables/datatables.min.js"></script>
    <script src="./Font-Awesome-master/js/all.min.js"></script>
    <script src="./js/script.js"></script>
    <style>
        :root {
            --bs-success-rgb: 71, 222, 152 !important;
        }
        html, body {
            height: 100%;
            width: 100%;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }
        main {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        #page-container {
            flex: 1 1 auto;
            padding: 20px;
            overflow: auto;
        }
        #topNavBar {
            flex: 0 1 auto;
        }
        .thumbnail-img {
            width: 50px;
            height: 50px;
            margin: 2px;
        }
        .truncate-1 {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
        }
        .modal-dialog.large {
            width: 80% !important;
            max-width: unset;
        }
        .modal-dialog.mid-large {
            width: 50% !important;
            max-width: unset;
        }
        @media (max-width: 720px) {
            .modal-dialog.large, .modal-dialog.mid-large {
                width: 100% !important;
                max-width: unset;
            }
        }
        .navbar {
            padding: 0.5rem 1rem;
        }
        .navbar-brand {
            font-size: 1.25rem;
            display: flex;
            align-items: center;
        }
        .navbar-brand i {
            margin-right: 8px;
        }
        .navbar-toggler {
            border: none;
        }
        .navbar-nav .nav-link {
            font-size: 1rem;
            padding: 10px 20px;
            transition: background 0.3s;
        }
        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }
        .dropdown-menu {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
        }
        /* Dark Mode */
        .dark-mode {
            background-color: #1e1e1e;
            color: #fff;
        }
        .dark-mode .navbar {
            background-color: #343a40 !important;
        }
        .dark-mode .dropdown-menu {
            background-color: #3a3b3c;
        }
        .dark-mode .modal-content {
            background-color: #2b2b2b;
            color: #fff;
        }
        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.1rem;
            }
            .navbar-nav .nav-link {
                font-size: 0.9rem;
                padding: 8px 15px;
            }
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #888;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <main>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary bg-gradient" id="topNavBar">
            <div class="container">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($page == 'home') ? 'active' : '' ?>" href="./"><i class="fas fa-home"></i> Home</a>
                        </li>
                        <?php if ($_SESSION['type'] == 1): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($page == 'customer') ? 'active' : '' ?>" href="./?page=customer"><i class="fas fa-users"></i> Customers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($page == 'balances') ? 'active' : '' ?>" href="./?page=balances"><i class="fas fa-balance-scale"></i> Balances</a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($page == 'sales') ? 'active' : '' ?>" href="./?page=sales"><i class="fas fa-cash-register"></i> POS</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($page == 'sales_report') ? 'active' : '' ?>" href="./?page=sales_report"><i class="fas fa-chart-line"></i> Sales Report</a>
                        </li>
                        <?php if ($_SESSION['type'] == 1): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($page == 'users') ? 'active' : '' ?>" href="./?page=users"><i class="fas fa-user-cog"></i> Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./?page=maintenance"><i class="fas fa-tools"></i> Maintenance</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle bg-transparent text-light border-0" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Hello, <?php echo $_SESSION['fullname'] ?>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="./?page=manage_account"><i class="fas fa-user-edit"></i> Manage Account</a></li>
                            <li><a class="dropdown-item" href="./Actions.php?a=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    <div class="container py-3" id="page-container">
        <?php 
            if(isset($_SESSION['flashdata'])):
        ?>
        <div class="dynamic_alert alert alert-<?php echo $_SESSION['flashdata']['type'] ?>">
        <div class="float-end"><a href="javascript:void(0)" class="text-dark text-decoration-none" onclick="$(this).closest('.dynamic_alert').hide('slow').remove()">x</a></div>
            <?php echo $_SESSION['flashdata']['msg'] ?>
        </div>
        <?php unset($_SESSION['flashdata']) ?>
        <?php endif; ?>
        <?php
            include $page.'.php';
        ?>
    </div>
    </main>
    <div class="modal fade" id="uni_modal" role='dialog' data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header py-2">
            <h5 class="modal-title"></h5>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer py-1">
            <button type="button" class="btn btn-sm rounded-0 btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
            <button type="button" class="btn btn-sm rounded-0 btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
        </div>
    </div>
    <div class="modal fade" id="uni_modal_secondary" role='dialog' data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header py-2">
            <h5 class="modal-title"></h5>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer py-1">
            <button type="button" class="btn btn-sm rounded-0 btn-primary" id='submit' onclick="$('#uni_modal_secondary form').submit()">Save</button>
            <button type="button" class="btn btn-sm rounded-0 btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
        </div>
    </div>
    <div class="modal fade" id="confirm_modal" role='dialog'>
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header py-2">
            <h5 class="modal-title">Confirmation</h5>
        </div>
        <div class="modal-body">
            <div id="delete_content"></div>
        </div>
        <div class="modal-footer py-1">
            <button type="button" class="btn btn-primary btn-sm rounded-0" id='confirm' onclick="">Continue</button>
            <button type="button" class="btn btn-secondary btn-sm rounded-0" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
        </div>
    </div>
</body>
</html>