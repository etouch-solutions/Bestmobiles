<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'mobile_insurance'; // Update this if needed

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add or update branch
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['branch_name']);
    $status = $_POST['status'];
    $branchId = $_POST['branch_id'] ?? '';

    if (!empty($name) && !empty($status)) {
        if ($branchId) {
            $stmt = $conn->prepare("UPDATE Branch_Master SET Branch_Name=?, Status=? WHERE Branch_Id=?");
            $stmt->bind_param("ssi", $name, $status, $branchId);
        } else {
            $stmt = $conn->prepare("INSERT INTO Branch_Master (Branch_Name, Status) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $status);
        }
        $stmt->execute();
        header("Location: branch_master.php");
        exit();
    }
}

// Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM Branch_Master WHERE Branch_Id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: branch_master.php");
    exit();
}

// Edit
$editBranch = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM Branch_Master WHERE Branch_Id = $id");
    if ($result->num_rows > 0) {
        $editBranch = $result->fetch_assoc();
    }
}

// Fetch all
$branches = $conn->query("SELECT * FROM Branch_Master ORDER BY Branch_Id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Branch Master</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .form-box, .list-box {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin: 20px;
        }
        .action-buttons a {
            margin-right: 10px;
            text-decoration: none;
            padding: 4px 8px;
            border-radius: 4px;
            color: #fff;
            font-size: 12px;
        }
        .edit-btn {
            background-color: #3498db;
        }
        .delete-btn {
            background-color: #e74c3c;
        }
    </style>
</head>
<body>

<h2 style="text-align: center;">Branch Master</h2>

<main style="display: flex; justify-content: space-around; flex-wrap: wrap;">
    <!-- Form -->
    <div class="form-box" style="flex: 1; min-width: 300px;">
        <h3><?= $editBranch ? 'Edit Branch' : 'Add Branch' ?></h3>
        <form method="POST">
            <input type="hidden" name="branch_id" value="<?= $editBranch['Branch_Id'] ?? '' ?>">
            <input type="text" name="branch_name" placeholder="Branch Name" value="<?= $editBranch['Branch_Name'] ?? '' ?>" required style="width: 100%; margin-bottom: 10px; padding: 8px;">
            <select name="status" required style="width: 100%; margin-bottom: 10px; padding: 8px;">
                <option value="">Select Status</option>
                <option value="Active" <?= (isset($editBranch) && $editBranch['Status'] == 'Active') ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= (isset($editBranch) && $editBranch['Status'] == 'Inactive') ? 'selected' : '' ?>>Inactive</option>
            </select>
            <button type="submit" style="width: 100%; padding: 10px; background-color: #27ae60; color: white; border: none; border-radius: 5px;">
                <?= $editBranch ? 'Update Branch' : 'Add Branch' ?>
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="list-box" style="flex: 2; min-width: 500px;">
        <h3>Branch Overview</h3>
        <input type="text" id="search" placeholder="Search by name..." style="width: 100%; padding: 8px; margin-bottom: 10px;">
        <table width="100%" border="1" cellspacing="0" cellpadding="10">
            <thead style="background: #ecf0f1;">
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="branchTable">
                <?php while ($row = $branches->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Branch_Name']) ?></td>
                    <td style="color:<?= $row['Status'] == 'Active' ? 'green' : 'red' ?>;">
                        <?= $row['Status'] ?>
                    </td>
                    <td class="action-buttons">
                        <a href="?edit=<?= $row['Branch_Id'] ?>" class="edit-btn">Edit</a>
                        <a href="?delete=<?= $row['Branch_Id'] ?>" class="delete-btn" onclick="return confirm('Delete this branch?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
document.getElementById("search").addEventListener("keyup", function () {
    var keyword = this.value.toLowerCase();
    var rows = document.querySelectorAll("#branchTable tr");
    rows.forEach(function (row) {
        var name = row.children[0].innerText.toLowerCase();
        row.style.display = name.includes(keyword) ? "" : "none";
    });
});
</script>

</body>
</html>
