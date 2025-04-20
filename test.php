<?php
include 'db_conn.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Query to fetch entries
$query = !empty($search)
    ? "SELECT id, name, amount_description, total_amount FROM ex_table WHERE name LIKE '%$search%' ORDER BY id DESC"
    : "SELECT id, name, amount_description, total_amount FROM ex_table ORDER BY id DESC";

$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Manager</title>
    <link rel="stylesheet" href="path/to/bootstrap.css">
    <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>

    <!-- Entry Table -->
    <div class="expense-entry-table">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Total Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($entry = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $entry['id'] ?></td>
                        <td><?= $entry['name'] ?></td>
                        <td><?= $entry['amount_description'] ?></td>
                        <td><?= $entry['total_amount'] ?></td>
                        <td>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editEntryModal-<?= $entry['id'] ?>">Edit Entry</button>

                            <a href="expense_page.php?id=<?= $entry['id'] ?>" class="btn btn-success btn-sm">Open</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<!--Edit Entry-->
<div class="modal fade" id="editEntryModal-<?= $entry['id'] ?>" tabindex="-1"
        aria-labelledby="editEntryModalLabel-<?= $entry['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEntryModalLabel-<?= $entry['id'] ?>">Edit Entry for ID
                        <?= $entry['id'] ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="process_edit_entry.php?id=<?= $entry['id'] ?>">
                        <div class="mb-3">
                            <label for="name-<?= $entry['id'] ?>" class="form-label">Name:</label>
                            <input type="text" class="form-control" id="name-<?= $entry['id'] ?>" name="name"
                                value="<?= $entry['name'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount_description-<?= $entry['id'] ?>" class="form-label">Description</label>
                            <input type="text" class="form-control" id="amount_description-<?= $entry['id'] ?>"
                                name="amount_description" value="<?= $entry['amount_description'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="total_amount-<?= $entry['id'] ?>" class="form-label">Total
                                Amount:</label>
                            <input type="number" class="form-control" id="total_amount-<?= $entry['id'] ?>"
                                name="total_amount" value="<?= $entry['total_amount'] ?>" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-warning">Update Entry</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get the edit button and modal
        var editButtons = document.querySelectorAll('.btn-warning[data-bs-toggle="modal"]');
        
        editButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var targetModalId = button.getAttribute('data-bs-target');
                var targetModal = new bootstrap.Modal(document.querySelector(targetModalId));
                targetModal.show();
            });
        });
    });
</script>

</body>
</html>
