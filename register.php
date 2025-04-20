<?php
include 'db_conn.php'; // Include database connection

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password and confirm password
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Hash the password using MD5
        $hashed_password = md5($password);

        // Check if username already exists
        $sql = "SELECT * FROM user WHERE Username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username already exists!";
        } else {
            // Insert new user into database
            $sql = "INSERT INTO user (Username, Password) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = "Error occurred. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
            font-family: 'Sansation-bold';
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
        .alert-danger, .alert-success {
            color: #ffffff;
        }
        .alert-danger {
            background-color: #b71c1c;
        }
        .alert-success {
            background-color: #388e3c;
        }
        a {
            color: #1E1E1E;
            font-family: 'Sansation-regular';
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
                    Register
                </div>
                <div class="card-body">
                    <!-- Error Message -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <!-- Success Message -->
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $success ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <!-- Registration Form -->
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="confirm password" required>
                        </div>
                        <button type="submit" class="btn btn-custom w-100">Register</button>
                    </form>
                    <!-- Link to Login -->
                    <div class="text-center mt-3">
                        <a href="login.php">Already have an account? Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
