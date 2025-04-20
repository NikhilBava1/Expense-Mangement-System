<?php
include('db_conn.php');
$id = $_GET['id'];

$query = "SELECT * FROM ex_table WHERE id = $id";
$result = $conn->query($query);
$entry = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];

    // Update total_amount and remaining
    $new_total = $entry['total_amount'] + $amount;
    $new_remaining = $entry['remaining'] + $amount;

    $update_query = "UPDATE ex_table SET total_amount = $new_total, remaining = $new_remaining WHERE id = $id";
    if ($conn->query($update_query) === TRUE) {
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
    <title>Add Amount</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2 class="text-center text-info">Add Amount</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="amount" class="form-label">Amount:</label>
                <input type="number" class="form-control" name="amount" required>
            </div>
            <button type="submit" class="btn btn-info">Add Amount</button>
        </form>
    </div>
</body>
</html>
