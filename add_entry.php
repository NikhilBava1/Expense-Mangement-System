<?php
include('db_conn.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $total_amount = $_POST['total_amount'];
    $amount_description = $_POST['amount_description'];
    
    // Insert the new entry into the database
    $insert_query = "INSERT INTO ex_table (name, total_amount, amount_description) 
                     VALUES ('$name', $total_amount, '$amount_description')";
    
    if ($conn->query($insert_query) === TRUE) {
        // Redirect to index page after successful insertion
        header('Location: index.php');
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Entry</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2 class="text-center text-success">New Entry</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="mb-3">
                <label for="total_amount" class="form-label">Total Amount:</label>
                <input type="number" class="form-control" name="total_amount" required>
            </div>
            <div class="mb-3">
                <label for="amount_description" class="form-label">Amount Description:</label>
                <textarea class="form-control" name="amount_description" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-success">Add Entry</button>
        </form>
    </div>
</body>
</html>
