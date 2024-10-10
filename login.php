<?php
session_start();
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) {
    header("Location:./");
    exit;
}
require_once('DBConnection.php');
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN | C & E FUEL STATION</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/popper.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/script.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            height: 100vh;
            display: flex;
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f0f4ff;
        }

        .login-container {
            display: flex;
            width: 100%;
            max-width: 900px;
            margin: auto;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .login-form {
            flex: 1;
            padding: 40px;
            background-color: #ffffff;
        }

        .login-form h2 {
            margin-bottom: 20px;
            font-weight: bold;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 10px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn-primary {
            width: 100%;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            padding: 10px;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .login-form,
            .illustration {
                width: 100%;
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-form">
            <h2>C & E FUEL STATION</h2>
            <form action="" id="login-form">
                <div class="form-group">
                    <input type="text" id="username" name="username" class="form-control" placeholder="Username or email" required>
                </div>
                <div class="form-group">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function () {
            $('#login-form').submit(function (e) {
                e.preventDefault();
                var _this = $(this);
                _this.find('button').attr('disabled', true).text('Logging in...');

                $.ajax({
                    url: './Actions.php?a=login',
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'JSON',
                    error: err => {
                        console.log(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'An error occurred.',
                            position: 'center', // Center the alert
                            backdrop: 'rgba(0,0,0,0.5)' // Optional: Dark background
                        });
                        _this.find('button').attr('disabled', false).text('Login');
                    },
                    success: function (resp) {
                        if (resp.status == 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Login successful!',
                                position: 'center', // Center the alert
                                showConfirmButton: false,
                                timer: 1500
                            });
                            setTimeout(() => {
                                location.replace('./'); // Redirect after SweetAlert
                            }, 1500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Login failed',
                                text: resp.msg,
                                position: 'center', // Center the alert
                                backdrop: 'rgba(0,0,0,0.5)' // Optional: Dark background
                            });
                        }
                        _this.find('button').attr('disabled', false).text('Login');
                    }
                });
            });
        });
    </script>
</body>

</html>
