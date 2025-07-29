<?php
include 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

// Insert / Update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['cus_id'] ?? null;
    $name = $_POST['cus_name'];
    $cno = $_POST['cus_cno'];
    $address = $_POST['cus_address'];
    $email = $_POST['cus_email'];
    $refName = $_POST['cus_ref'];
    $refCno = $_POST['cus_ref_cno'];
    $branchId = $_POST['branch_id'];
    $status = $_POST['cus_status'];

    // File Uploads
    $photoPath = $_POST['existing_photo'] ?? '';
    if (!empty($_FILES['cus_photo']['name'])) {
        $photoPath = $uploadDir . time() . '_' . $_FILES['cus_photo']['name'];
        move_uploaded_file($_FILES['cus_photo']['tmp_name'], $photoPath);
    }

    $idCopyPath = $_POST['existing_id_copy'] ?? '';
    if (!empty($_FILES['cus_id_copy']['name'])) {
        $idCopyPath = $uploadDir . time() . '_' . $_FILES['cus_id_copy']['name'];
        move_uploaded_file($_FILES['cus_id_copy']['tmp_name'], $idCopyPath);
    }

    if ($id) {
        $stmt = $conn->prepare("UPDATE Customer_Master SET Cus_Name=?, Cus_CNo=?, Cus_Address=?, Cus_Email=?, Cus_Ref_Name=?, Cus_Ref_CNo=?, Branch_Id=?, Cus_Photo_Path=?, Cus_Id_Copy_Path=?, Is_Active=? WHERE Cus_Id=?");
        $stmt->bind_param("sisssisssii", $name, $cno, $address, $email, $refName, $refCno, $branchId, $photoPath, $idCopyPath, $status, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO Customer_Master (Cus_Name, Cus_CNo, Cus_Address, Cus_Email, Cus_Ref_Name, Cus_Ref_CNo, Branch_Id, Cus_Photo_Path, Cus_Id_Copy_Path, Is_Active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssisssi", $name, $cno, $address, $email, $refName, $refCno, $branchId, $photoPath, $idCopyPath, $status);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: Customer_Master.php?saved=1");
    exit;
}

// Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM Customer_Master WHERE Cus_Id = $id");
    header("Location: Customer_Master.php?deleted=1");
    exit;
}

// Edit fetch
$editData = null;
if (isset($_GET['edit'])) {
    $res = $conn->query("SELECT * FROM Customer_Master WHERE Cus_Id = " . intval($_GET['edit']));
    if ($res && $res->num_rows > 0) $editData = $res->fetch_assoc();
}

// Search
$search = $_GET['search'] ?? '';
$searchSql = $search ? "WHERE Cus_Name LIKE '%$search%' OR Cus_CNo LIKE '%$search%'" : "";
$customers = $conn->query("SELECT c.*, b.Branch_Name FROM Customer_Master c LEFT JOIN Branch_Master b ON c.Branch_Id = b.Branch_Id $searchSql ORDER BY Cus_Id DESC");

// Branches
$branches = $conn->query("SELECT * FROM Branch_Master WHERE Branch_Status = 1");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Master</title>
    <style>
        body { font-family: Arial; display: flex; margin: 0; }
        .sidebar, .preview { width: 25%; padding: 20px; background: #fafafa; height: 100vh; overflow-y: auto; border-right: 1px solid #ccc; }
        .main { flex: 1; padding: 20px; }
        input, textarea, select { width: 100%; padding: 8px; margin-bottom: 10px; }
        .item { padding: 10px; border-bottom: 1px solid #eee; cursor: pointer; }
        .item:hover { background: #f0f0f0; }
        .actions a { margin-right: 10px; color: blue; text-decoration: none; }
        .actions a.delete { color: red; }
        img.preview-img { width: 100px; height: auto; margin-top: 10px; }
    </style>
    <script>
        function showPreview(data) {
            document.getElementById('preview').innerHTML = `
                <h3>Customer Details</h3>
                <b>Name:</b> ${data.name}<br>
                <b>Contact:</b> ${data.cno}<br>
                <b>Email:</b> ${data.email}<br>
                <b>Address:</b> ${data.address}<br>
                <b>Reference:</b> ${data.ref}<br>
                <b>Ref CNo:</b> ${data.ref_cno}<br>
                <b>Branch:</b> ${data.branch}<br>
                <b>Status:</b> ${data.status == 1 ? 'Active' : 'Inactive'}<br>
                ${data.photo ? `<b>Photo:</b><br><img class="preview-img" src="${data.photo}"><br>` : ''}
                ${data.id_copy ? `<b>ID Copy:</b><br><img class="preview-img" src="${data.id_copy}"><br>` : ''}
            `;
        }
        function confirmDelete(id) {
            if (confirm("Delete this customer?")) window.location.href = "?delete=" + id;
        }
    </script>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h3>Customers</h3>
    <form method="get">
        <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
    </form>
    <hr>
    <?php while ($row = $customers->fetch_assoc()): ?>
        <div class="item" onclick='showPreview({
            name: "<?= addslashes($row["Cus_Name"]) ?>",
            cno: "<?= $row["Cus_CNo"] ?>",
            email: "<?= addslashes($row["Cus_Email"]) ?>",
            address: "<?= addslashes($row["Cus_Address"]) ?>",
            ref: "<?= addslashes($row["Cus_Ref_Name"]) ?>",
            ref_cno: "<?= $row["Cus_Ref_CNo"] ?>",
            status: "<?= $row["Is_Active"] ?>",
            branch: "<?= addslashes($row["Branch_Name"]) ?>",
            photo: "<?= $row["Cus_Photo_Path"] ?>",
            id_copy: "<?= $row["Cus_Id_Copy_Path"] ?>"
        })'>
            <?= htmlspecialchars($row['Cus_Name']) ?>
            <div class="actions">
                <a href="?edit=<?= $row['Cus_Id'] ?>">Edit</a>
                <a href="javascript:void(0)" class="delete" onclick="confirmDelete(<?= $row['Cus_Id'] ?>)">Delete</a>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<!-- Main -->
<div class="main">
    <h2><?= $editData ? "Edit Customer" : "Add Customer" ?></h2>
    <form method="POST" enctype="multipart/form-data">
        <?php if ($editData): ?>
            <input type="hidden" name="cus_id" value="<?= $editData['Cus_Id'] ?>">
            <input type="hidden" name="existing_photo" value="<?= $editData['Cus_Photo_Path'] ?>">
            <input type="hidden" name="existing_id_copy" value="<?= $editData['Cus_Id_Copy_Path'] ?>">
        <?php endif; ?>

        <label>Name:</label>
        <input type="text" name="cus_name" required value="<?= $editData['Cus_Name'] ?? '' ?>">

        <label>Contact No:</label>
        <input type="number" name="cus_cno" required value="<?= $editData['Cus_CNo'] ?? '' ?>">

        <label>Address:</label>
        <textarea name="cus_address" required><?= $editData['Cus_Address'] ?? '' ?></textarea>

        <label>Email:</label>
        <input type="email" name="cus_email" value="<?= $editData['Cus_Email'] ?? '' ?>">

        <label>Reference Name:</label>
        <input type="text" name="cus_ref" value="<?= $editData['Cus_Ref_Name'] ?? '' ?>">

        <label>Reference Contact No:</label>
        <input type="number" name="cus_ref_cno" value="<?= $editData['Cus_Ref_CNo'] ?? '' ?>">

        <label>Branch:</label>
        <select name="branch_id" required>
            <option value="">-- Select Branch --</option>
            <?php while ($b = $branches->fetch_assoc()): ?>
                <option value="<?= $b['Branch_Id'] ?>" <?= (isset($editData['Branch_Id']) && $editData['Branch_Id'] == $b['Branch_Id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($b['Branch_Name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Customer Photo:</label>
        <input type="file" name="cus_photo" accept="image/*">

        <label>ID Copy:</label>
        <input type="file" name="cus_id_copy" accept="image/*">

        <label>Status:</label>
        <select name="cus_status">
            <option value="1" <?= (isset($editData['Is_Active']) && $editData['Is_Active'] == 1) ? 'selected' : '' ?>>Active</option>
            <option value="0" <?= (isset($editData['Is_Active']) && $editData['Is_Active'] == 0) ? 'selected' : '' ?>>Inactive</option>
        </select>

        <button type="submit"><?= $editData ? 'Update' : 'Add' ?> Customer</button>
    </form>
</div>

<!-- Preview -->
<div class="preview" id="preview">
    <h3>Customer Details</h3>
    <p>Select a customer to preview.</p>
</div>

</body>
</html>
