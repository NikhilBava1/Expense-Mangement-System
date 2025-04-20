<?php
session_start();
include 'db_conn.php'; // Include database connection

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Hash the raw password input

    // Use prepared statements to prevent SQL injection
    $sql = "SELECT * FROM user WHERE Username = ? AND Password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['username'] = $username;
        header('Location: index.php');
        exit();
    } else {
        $error = "Invalid Username or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: 'Sansation-bold';
            src: url('./fonts/Sansation_Bold.ttf')format('truetype');
        }
        @font-face {
            font-family: 'Sansation-regular';
            src: url('./fonts/Sansation_Regular.ttf')format('truetype');
        }
        @font-face {
            font-family: 'Sansation-light';
            src: url('./fonts/Sansation_Light.ttf')format('truetype');
        }
        body {
            background-color: #1E1E1E;
            color: #1E1E1E;
            font-family: 'Sansation-Bold';
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #ffffff;
            border: none;
            border-radius: 15px;
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.5);
            color: #1E1E1E;
        }
        .card-header {
            background-color: #78FF00;
            border-bottom: none;
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            color: #1E1E1E;
        }
        .btn-custom {
            background-color: #78FF00;
            border: none;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #57B602;
        }
        .form-label {
            color: #1E1E1E;
            font-family: 'Sansation-regular';
        }
        input::placeholder{
            font-family: 'Sansation-light';
            font-size: 14px;
        }
        input{
            font-family: 'Sansation-regular';
        }
        .alert-danger {
            background-color: #b71c1c;
            color: #ffffff;
        }
        a {
            font-family: 'Sansation-regular';
            color: #1E1E1E;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            width: auto;
            max-width: 300px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card p-4">
                <div class="card-header">
                    Login
                </div>
                <div class="card-body">
                    <!-- Error Message -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <!-- Login Form -->
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="password" required>
                        </div>
                        <div class="text-center mt-4 mb-4">
                            <a href="register.php">Don't have an account? Register</a>
                        </div>
                        <button type="submit" class="btn btn-custom w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
