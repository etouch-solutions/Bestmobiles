<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

$conn = mysqli_connect($host, $user, $pass, $db);

// Create upload folders if not exist
@mkdir('uploads/photos', 0777, true);
@mkdir('uploads/idcopies', 0777, true);

// Insert or Update Customer
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['cus_id'] ?? null;
    $name = $_POST['cus_name'];
    $cno = $_POST['cus_cno'];
    $email = $_POST['cus_email'];
    $address = $_POST['cus_address'];
    $ref_name = $_POST['cus_ref'];
    $ref_cno = $_POST['cus_ref_cno'];
    $branch_id = $_POST['branch_id'];
    $status = $_POST['cus_status'];

    $photo_path = $_FILES['cus_photo']['error'] == 0
        ? 'uploads/photos/' . time() . '_' . basename($_FILES['cus_photo']['name'])
        : ($_POST['existing_photo'] ?? '');

    $id_copy_path = $_FILES['cus_id_copy']['error'] == 0
        ? 'uploads/idcopies/' . time() . '_' . basename($_FILES['cus_id_copy']['name'])
        : ($_POST['existing_idcopy'] ?? '');

    if ($_FILES['cus_photo']['error'] == 0) {
        move_uploaded_file($_FILES['cus_photo']['tmp_name'], $photo_path);
    }

    if ($_FILES['cus_id_copy']['error'] == 0) {
        move_uploaded_file($_FILES['cus_id_copy']['tmp_name'], $id_copy_path);
    }

    if ($id) {
        $stmt = $conn->prepare("UPDATE Customer_Master SET Cus_Name=?, Cus_CNo=?, Cus_Email=?, Cus_Address=?, Cus_Ref_Name=?, Cus_Ref_CNo=?, Branch_Id=?, Is_Active=?, Cus_Photo_Path=?, Cus_Id_Copy_Path=? WHERE Cus_Id=?");
        $stmt->bind_param("sisssiiissi", $name, $cno, $email, $address, $ref_name, $ref_cno, $branch_id, $status, $photo_path, $id_copy_path, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO Customer_Master (Cus_Name, Cus_CNo, Cus_Email, Cus_Address, Cus_Ref_Name, Cus_Ref_CNo, Branch_Id, Is_Active, Cus_Photo_Path, Cus_Id_Copy_Path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssiiiss", $name, $cno, $email, $address, $ref_name, $ref_cno, $branch_id, $status, $photo_path, $id_copy_path);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: Customer_Master.php?saved=1");
    exit;
}

// Delete
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    $check = $conn->query("SELECT * FROM Insurance_Entry WHERE Cus_Id = $deleteId");
    if ($check->num_rows > 0) {
        echo "<script>alert('Cannot delete: Customer has insurance.'); window.location.href='Customer_Master.php';</script>";
        exit;
    }
    $conn->query("DELETE FROM Customer_Master WHERE Cus_Id = $deleteId");
    echo "<script>alert('Customer deleted successfully'); window.location.href='Customer_Master.php';</script>";
    exit;
}

// Edit
$editData = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM Customer_Master WHERE Cus_Id = $editId");
    if ($res->num_rows > 0) {
        $editData = $res->fetch_assoc();
    }
}

// Fetch branches
$branches = $conn->query("SELECT Branch_Id, Branch_Name FROM Branch_Master");

// Search customers
$search = $_GET['search'] ?? '';
$searchSql = $search ? "WHERE Cus_Name LIKE '%$search%' OR Cus_CNo LIKE '%$search%'" : "";
$customers = $conn->query("SELECT * FROM Customer_Master $searchSql ORDER BY Cus_Id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Master</title>
    <style>
        body { font-family: Arial; display: flex; background: #f2f2f2; margin: 0; }
        .sidebar, .preview { width: 25%; padding: 20px; background: #fff; border-right: 1px solid #ccc; height: 100vh; overflow-y: auto; }
        .main { flex: 1; padding: 20px; background: #f9f9f9; }
        input, select, textarea { width: 100%; padding: 8px; margin-bottom: 10px; }
        .item { padding: 8px; cursor: pointer; border-bottom: 1px solid #ddd; }
        .item:hover { background-color: #e8e8e8; }
        .actions a { margin-right: 10px; color: blue; text-decoration: none; }
        .actions a.delete { color: red; }
    </style>
    <script>
        function showPreview(data) {
            document.getElementById('preview').innerHTML = `
                <h3>Customer Details</h3>
                <p><b>Name:</b> ${data.name}</p>
                <p><b>Contact:</b> ${data.cno}</p>
                <p><b>Email:</b> ${data.email}</p>
                <p><b>Address:</b> ${data.address}</p>
                <p><b>Reference:</b> ${data.refname} (${data.refcno})</p>
                <p><b>Status:</b> ${data.status == 1 ? 'Active' : 'Inactive'}</p>
                <p><b>Branch ID:</b> ${data.branch}</p>
                <p><b>Photo:</b><br><img src='${data.photo}' width='100'></p>
                <p><b>ID Copy:</b><br><img src='${data.idcopy}' width='100'></p>
            `;
        }
    </script>
</head>
<body>

<div class="sidebar">
    <h3>Customers</h3>
    <form method="get">
        <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
    </form>
    <hr>
    <?php while ($row = $customers->fetch_assoc()): ?>
        <?php
        $data = [
            "name" => addslashes($row['Cus_Name']),
            "cno" => $row['Cus_CNo'],
            "email" => addslashes($row['Cus_Email']),
            "address" => addslashes($row['Cus_Address']),
            "refname" => addslashes($row['Cus_Ref_Name']),
            "refcno" => $row['Cus_Ref_CNo'],
            "status" => $row['Is_Active'],
            "branch" => $row['Branch_Id'],
            "photo" => $row['Cus_Photo_Path'],
            "idcopy" => $row['Cus_Id_Copy_Path']
        ];
        ?>
        <div class="item" onclick='showPreview(<?= json_encode($data) ?>)'>
            <b><?= htmlspecialchars($row['Cus_Name']) ?></b>
            <div class="actions">
                <a href="?edit=<?= $row['Cus_Id'] ?>">Edit</a>
                <a href="?delete=<?= $row['Cus_Id'] ?>" class="delete">Delete</a>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<div class="main">
    <h2><?= $editData ? 'Edit Customer' : 'Add Customer' ?></h2>
    <form method="POST" enctype="multipart/form-data">
        <?php if ($editData): ?>
            <input type="hidden" name="cus_id" value="<?= $editData['Cus_Id'] ?>">
            <input type="hidden" name="existing_photo" value="<?= $editData['Cus_Photo_Path'] ?>">
            <input type="hidden" name="existing_idcopy" value="<?= $editData['Cus_Id_Copy_Path'] ?>">
        <?php endif; ?>

        <label>Name</label>
        <input type="text" name="cus_name" required value="<?= $editData['Cus_Name'] ?? '' ?>">

        <label>Contact Number</label>
        <input type="number" name="cus_cno" required value="<?= $editData['Cus_CNo'] ?? '' ?>">

        <label>Email</label>
        <input type="email" name="cus_email" value="<?= $editData['Cus_Email'] ?? '' ?>">

        <label>Address</label>
        <textarea name="cus_address" required><?= $editData['Cus_Address'] ?? '' ?></textarea>

        <label>Reference Name</label>
        <input type="text" name="cus_ref" value="<?= $editData['Cus_Ref_Name'] ?? '' ?>">

        <label>Reference Contact Number</label>
        <input type="number" name="cus_ref_cno" value="<?= $editData['Cus_Ref_CNo'] ?? '' ?>">

        <label>Branch</label>
        <select name="branch_id" required>
            <option value="">-- Select Branch --</option>
            <?php while ($b = $branches->fetch_assoc()): ?>
                <option value="<?= $b['Branch_Id'] ?>" <?= isset($editData['Branch_Id']) && $editData['Branch_Id'] == $b['Branch_Id'] ? 'selected' : '' ?>>
                    <?= $b['Branch_Name'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Status</label>
        <select name="cus_status">
            <option value="1" <?= isset($editData['Is_Active']) && $editData['Is_Active'] == 1 ? 'selected' : '' ?>>Active</option>
            <option value="0" <?= isset($editData['Is_Active']) && $editData['Is_Active'] == 0 ? 'selected' : '' ?>>Inactive</option>
        </select>

        <label>Customer Photo</label>
        <input type="file" name="cus_photo">

        <label>ID Copy</label>
        <input type="file" name="cus_id_copy">

        <button type="submit"><?= $editData ? 'Update Customer' : 'Add Customer' ?></button>
    </form>
</div>

<div class="preview" id="preview">
    <h3>Customer Details</h3>
    <p>Select a customer to preview.</p>
</div>

</body>
</html>
