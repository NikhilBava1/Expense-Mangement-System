<!-- Main Entries Table -->
    <div class="table-container">
        <?php
        $query = "SELECT * FROM ex_table";
        $result = $conn->query($query);


        // Fetch all records in reverse order (latest entry first)
        $query = "SELECT * FROM ex_table ORDER BY id DESC";
        $result = $conn->query($query);
        while ($entry = $result->fetch_assoc()) {
            $id = $entry['id'];
            $expense_query = "SELECT * FROM expenses WHERE ex_id = '$id'";
            $expense_result = $conn->query($expense_query);

            $total_expenses = 0;
            $expenses = [];
            while ($expense = $expense_result->fetch_assoc()) {
                $expenses[] = $expense;
                $total_expenses += $expense['expense'];
            }

            $remaining = $entry['total_amount'] - $total_expenses;
            ?>
            <!-- Entry Table (Each Entry gets its own Table) -->
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
                        <tr>
                            <td><?= $entry['id'] ?></td>
                            <td><?= $entry['name'] ?></td>
                            <td><?= $entry['amount_description'] ?></td>
                            <td><?= $entry['total_amount'] ?></td>
                            <td id="remaining-<?= $entry['id'] ?>"><?= $remaining ?></td>
                            <td>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#editEntryModal-<?= $entry['id'] ?>">
                                    Edit Entry
                                </button>

                            </td>
                        </tr>
                    </tbody>
                </table>


                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Expense</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="expense-body-<?= $entry['id'] ?>">
                        <?php foreach ($expenses as $expense) { ?>
                            <tr data-expense-id="<?= $expense['id'] ?>">
                                <td ondblclick="makeEditable(this)" data-id="<?= $expense['id'] ?>" data-column="expense"
                                    class="editable"><?= $expense['expense'] ?></td>
                                <td ondblclick="makeEditable(this)" data-id="<?= $expense['id'] ?>" data-column="description"
                                    class="editable"><?= $expense['description'] ?></td>
                                <td class="actions-btn">
                                    <button onclick="showDeleteModal(<?= $expense['id'] ?>, <?= $entry['id'] ?>)"
                                        class="btn btn-danger btn-sm">Delete</button>
                                    <button class="btn btn-success btn-sm save-btn" style="display:none;"
                                        onclick="saveEdit(this, <?= $expense['id'] ?>)">Save</button>
                                </td>
                            </tr>
                        <?php } ?>
                        <!-- Last Row for Total Expense -->
                        <tr class="table-warning">
                            <td colspan="2" class="text-start"><strong>Total Expense:</strong></td>
                            <td><strong><?= $total_expenses ?></strong></td>
                        </tr>
                    </tbody>

                </table>
                <!-- Add Expense Button -->
                <div class="text-end">
                    <button class="btn btn-primary btn-sm" onclick="addExpense(<?= $entry['id'] ?>)">Add
                        Expense</button>
                </div>
            <?php } ?>
        </div>
    </div>