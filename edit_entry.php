<?php
include('db_conn.php');

// Check if 'id' is present in URL query string
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch the entry data using the id
    $query = "SELECT * FROM ex_table WHERE id = $id";
    $result = $conn->query($query);
    $entry = $result->fetch_assoc();
} else {
    // Redirect to index if no ID is provided
    header('Location: index.php');
    exit;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $total_amount = $_POST['total_amount'];
    $amount_description = $_POST['amount_description'];
    
    // Update the entry with the new data
    $update_query = "UPDATE ex_table 
                     SET name = '$name', amount_description = '$amount_description', total_amount = $total_amount 
                     WHERE id = $id";
    
    if ($conn->query($update_query) === TRUE) {
        // Redirect to index page after successful update
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
    <title>Edit Entry</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2 class="text-center text-warning">Edit Entry</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" class="form-control" name="name" value="<?= $entry['name'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="total_amount" class="form-label">Total Amount:</label>
                <input type="number" class="form-control" name="total_amount" value="<?= $entry['total_amount'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="amount_description" class="form-label">Amount Description:</label>
                <textarea class="form-control" name="amount_description" rows="3" required><?= $entry['amount_description'] ?></textarea>
            </div>
            <button type="submit" class="btn btn-warning">Update Entry</button>
        </form>
    </div>
</body>
</html>
