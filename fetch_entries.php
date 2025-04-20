<?php
include 'db_conn.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Modify the query to include search condition
if (!empty($search)) {
    $query = "SELECT * FROM ex_table WHERE name LIKE '%$search%' ORDER BY id DESC";
} else {
    $query = "SELECT * FROM ex_table ORDER BY id DESC";
}
$result = $conn->query($query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Entries</title>
    <!-- Add Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
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
        .btn-border{
            border: 1.5px solid #282828;
        }
        .table{
            font-family: Sansation-bold;
        }
     
        #regular{
            font-family: Sansation-regular;
        }
        .editEntryModalform,.modal-title {
            font-family: Sansation-bold;
        }
        #editEntryModalform input::placeholder{
            font-family: Sansation-light;
        }
        .editEntryModalform input{
            font-family: Sansation-regular;
        }
        .btn-border:hover{
            background-color:rgb(216, 216, 216);
        }
    </style>
</head>

<body>

    <div class="container mt-4">
        <!-- Search Form -->
        <!-- <form method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by Name">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form> -->

        <!-- Entry Table -->
        <div class="expense-entry-table">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Total Amount</th>
                        <th>Remaining</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($entry = $result->fetch_assoc()) { 
                        $id = $entry['id'];
                        $expense_query = "SELECT * FROM expenses WHERE ex_id = '$id'";
                        $expense_result = $conn->query($expense_query);

                        $total_expenses = 0;
                        while ($expense = $expense_result->fetch_assoc()) {
                            $total_expenses += $expense['expense'];
                        }

                        $remaining = $entry['total_amount'] - $total_expenses;
                    ?>
                        <tr>
                            <td  id="regular"><?= $entry['id'] ?></td>
                            <td  id="regular"><?= $entry['name'] ?></td>
                            <td  id="regular"><?= $entry['amount_description'] ?></td>
                            <td  id="regular"><?= $entry['total_amount'] ?></td>
                            <td  id="regular" id="remaining-<?= $entry['id'] ?>"><?= $remaining ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-border" data-bs-toggle="modal" data-bs-target="#editEntryModal-<?= $entry['id'] ?>">
                                    Edit Entry
                                </button>
                                <a href="print.php?id=<?= $entry['id'] ?>" class="btn btn-primary btn-sm">Open</a>
                            </td>
                        </tr>

                        <!-- Edit Entry Modal -->
                        <div class="modal fade" class="editEntryModal" id="editEntryModal-<?= $entry['id'] ?>" tabindex="-1" aria-labelledby="editEntryModalLabel-<?= $entry['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editEntryModalLabel-<?= $entry['id'] ?>">Edit Entry for ID <?= $entry['id'] ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body editEntryModalform">
                                        <form method="POST" action="process_edit_entry.php?id=<?= $entry['id'] ?>">
                                            <div class="mb-3">
                                                <label for="name-<?= $entry['id'] ?>" class="form-label">Name:</label>
                                                <input type="text" class="form-control" id="name-<?= $entry['id'] ?>" name="name" value="<?= $entry['name'] ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="amount_description-<?= $entry['id'] ?>" class="form-label">Description</label>
                                                <input type="text" class="form-control" id="amount_description-<?= $entry['id'] ?>" name="amount_description" value="<?= $entry['amount_description'] ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="total_amount-<?= $entry['id'] ?>" class="form-label">Total Amount:</label>
                                                <input type="number" class="form-control" id="total_amount-<?= $entry['id'] ?>" name="total_amount" value="<?= $entry['total_amount'] ?>" required>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Update Entry</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
                    
</body>

</html>
