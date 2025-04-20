<?php
include 'db_conn.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';

    if ($action === 'add') {
        $ex_id = intval($data['ex_id']);
        $expense = floatval($data['expense']);
        $description = $data['description'];

        $insert_query = "INSERT INTO expenses (ex_id, expense, description) VALUES ($ex_id, $expense, '$description')";
        $result = $conn->query($insert_query);

        echo json_encode(['success' => $result, 'expense_id' => $conn->insert_id]);
        exit;
    }

    if ($action === 'delete') {
        $expense_id = intval($data['id']);

        $delete_query = "DELETE FROM expenses WHERE id = $expense_id";
        $result = $conn->query($delete_query);

        echo json_encode(['success' => $result]);
        exit;
    }

    if ($action === 'update') {
        $id = intval($data['id']);
        $column = $data['column'];
        $value = $data['value'];

        $update_query = "UPDATE expenses SET $column = '$value' WHERE id = $id";
        $result = $conn->query($update_query);

        echo json_encode(['success' => $result]);
        exit;
    }
}

// Fetch entry details
$entry_query = "SELECT name, total_amount FROM ex_table WHERE id = $id";
$entry_result = $conn->query($entry_query);
$entry = $entry_result->fetch_assoc();

// Fetch expenses for the entry
$expense_query = "SELECT id, expense, description FROM expenses WHERE ex_id = $id";
$expense_result = $conn->query($expense_query);

$total_expenses = 0;
$expenses = [];
while ($expense = $expense_result->fetch_assoc()) {
    $expenses[] = $expense;
    $total_expenses += $expense['expense'];
}

$remaining = $entry['total_amount'] - $total_expenses;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses for <?= $entry['name'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <style>
        @font-face {
            font-family: 'Sansation-bold';
            src: url('./fonts/Sansation_Bold.ttf') format('truetype');
        }

        @font-face {
            font-family: 'Sansation-regular';
            src: url('./fonts/Sansation_Regular.ttf') format('truetype');
        }

        @font-face {
            font-family: 'Sansation-light';
            src: url('./fonts/Sansation_Light.ttf') format('truetype');
        }

        body {
            background-color: #f8f9fa;
        }

        .table {
            font-family: Sansation-bold;
        }

        .container {
            margin-top: 20px;
        }

        .card-header {
            background-color: #007bff;
            color: white;
        }

        .editable:hover {
            background-color: #fff3cd;
            cursor: pointer;
        }

        .summary-table {
            width: 100%;
            margin-top: 20px;
        }

        .summary-table th,
        .summary-table td {
            vertical-align: middle;
            font-size: 1rem;
        }

        #light {
            font-family: Sansation-light;
        }

        #regular {
            font-family: Sansation-regular;
        }

        #addExpenseinput::placeholder {
            font-family: Sansation-light;
        }

        #addExpenseinput {
            font-family: Sansation-regular;
        }

        .card-header {
            background-color: #282828;
        }
    </style>
</head>

<body>
<div class="container">
    <!-- Entry Details -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <h4 class="mb-0">Expenses for <strong><?= $entry['name'] ?></strong></h4>
            <div>
                <button class="btn btn-success btn-sm" id="printPdfBtn">Save PDF</button>
                <button class="btn btn-primary btn-sm float-end me-2" id="printViewBtn">Print</button>
            </div>
        </div>
        <div class="card-body">
            <!-- Table for Total Budget and Remaining Amount -->
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                <tr>
                    <th>Total Budget (₹)</th>
                    <th>Remaining Amount (₹)</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td id="regular"><?= $entry['total_amount'] ?></td>
                    <td id="regular" id="remainingAmount"><?= $remaining ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Expense Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Expense Details</h5>
            <button class="btn btn-success btn-sm" id="addExpenseBtn">+ Add Expense</button>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover" id="expenseTable">
                <thead class="table-light">
                <tr>
                    <th>Expense (₹)</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($expenses as $expense) { ?>
                    <tr>
                        <td id="regular" ondblclick="makeEditable(this)" data-id="<?= $expense['id'] ?>" data-column="expense"
                            class="editable">
                            <?= $expense['expense'] ?>
                        </td>
                        <td id="regular" ondblclick="makeEditable(this)" data-id="<?= $expense['id'] ?>"
                            data-column="description" class="editable">
                            <?= $expense['description'] ?>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm"
                                    onclick="confirmDelete(<?= $expense['id'] ?>)">Delete</button>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <!-- Summary Table -->
            <div class="summary-card float-end pt-4" style="flex: 1; max-width: 300px;">
                <h5>Summary</h5>
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <th>Total Expense:</th>
                        <td><span id="totalAmount">₹0</span></td>
                    </tr>
                    <tr>
                        <th>Remaining Amount:</th>
                        <td><span id="remainingAmount">₹<?= $remaining ?></span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
        document.getElementById('printViewBtn').addEventListener('click', function () {
    // Select the container for print
    let element = document.querySelector('.container');

    // Clone the container to clean up unnecessary elements
    let clone = element.cloneNode(true);

    // Remove unnecessary buttons for Print view
    clone.querySelector('#printPdfBtn')?.remove();
    clone.querySelector('#printViewBtn')?.remove();
    clone.querySelector('#addExpenseBtn')?.remove();
    let actions = clone.querySelectorAll('th:nth-child(3), td:nth-child(3)');
    actions.forEach(action => action.remove());

    // Create a new print window
    let printWindow = window.open('', '_self');

    // Write the cloned HTML to the print window
    printWindow.document.open();
    printWindow.document.write(`
        <html>
            <head>
                <title>Expenses For <?= $entry['name'] ?></title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    @font-face {
                        font-family: 'Sansation-bold';
                        src: url('./fonts/Sansation_Bold.ttf') format('truetype');
                    }
                    @font-face {
                        font-family: 'Sansation-regular';
                        src: url('./fonts/Sansation_Regular.ttf') format('truetype');
                    }
                    @font-face {
                        font-family: 'Sansation-light';
                        src: url('./fonts/Sansation_Light.ttf') format('truetype');
                    }
                    body {
                        font-family: Sansation-regular;
                        margin: 10px;
                    }
                </style>
            </head>
            <body>${clone.outerHTML}</body>
        </html>
    `);
    printWindow.document.close();

    // Trigger print and close the window
    printWindow.onload = function () {
        printWindow.print();
        printWindow.onafterprint = function () {
            printWindow.close();
        };
    };
});

</script>
</body>

</html>
