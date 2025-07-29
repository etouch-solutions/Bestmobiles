<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

$conn = mysqli_connect($host, $user, $pass, $db);

// Handle Insert or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cus_id = $_POST['cus_id'] ?? null;
    $cus_name = $_POST['cus_name'];
    $cus_cno = $_POST['cus_cno'];
    $cus_address = $_POST['cus_address'];
    $cus_email = $_POST['cus_email'];
    $cus_ref = $_POST['cus_ref'];
    $cus_ref_cno = $_POST['cus_ref_cno'];
    $branch_id = $_POST['branch_id'];
    $status = $_POST['cus_status'];

    $cus_photo_path = '';
    $cus_id_copy_path = '';

    // Upload Photo
    if ($_FILES['cus_photo']['name']) {
        $targetDir = "uploads/customers/";
        $cus_photo_path = $targetDir . time() . '_' . basename($_FILES["cus_photo"]["name"]);
        move_uploaded_file($_FILES["cus_photo"]["tmp_name"], $cus_photo_path);
    }

    // Upload ID Copy
    if ($_FILES['cus_id_copy']['name']) {
        $targetDir = "uploads/ids/";
        $cus_id_copy_path = $targetDir . time() . '_' . basename($_FILES["cus_id_copy"]["name"]);
        move_uploaded_file($_FILES["cus_id_copy"]["tmp_name"], $cus_id_copy_path);
    }

    if ($cus_id) {
        // Update
        $stmt = $conn->prepare("UPDATE Customer_Master SET Cus_Name=?, Cus_CNo=?, Cus_Address=?, Cus_Email=?, Cus_Ref=?, Cus_Ref_CNo=?, Branch_Id=?, Customer_Status=? WHERE Cus_Id=?");
        $stmt->bind_param("sisssiiii", $cus_name, $cus_cno, $cus_address, $cus_email, $cus_ref, $cus_ref_cno, $branch_id, $status, $cus_id);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO Customer_Master (Cus_Name, Cus_CNo, Cus_Address, Cus_Email, Cus_Ref, Cus_Ref_CNo, Branch_Id, Customer_Status, Cus_Photo, Cus_Id_Copy) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssiiiss", $cus_name, $cus_cno, $cus_address, $cus_email, $cus_ref, $cus_ref_cno, $branch_id, $status, $cus_photo_path, $cus_id_copy_path);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: Customer_Master.php?saved=1");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM Customer_Master WHERE Cus_Id = $id");
    header("Location: Customer_Master.php?deleted=1");
    exit;
}

// Fetch one for editing
$editData = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM Customer_Master WHERE Cus_Id = $editId");
    if ($res->num_rows > 0) {
        $editData = $res->fetch_assoc();
    }
}

// Fetch Branches
$branches = $conn->query("SELECT * FROM Branch_Master WHERE Branch_Status = 1");

// Fetch All Customers (with optional search)
$search = $_GET['search'] ?? '';
$searchSql = $search ? "WHERE Cus_Name LIKE '%$search%' OR Cus_CNo LIKE '%$search%'" : "";
$customers = $conn->query("SELECT c.*, b.Branch_Name FROM Customer_Master c LEFT JOIN Branch_Master b ON c.Branch_Id = b.Branch_Id $searchSql ORDER BY Cus_Id DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Customer Master</title>
  <style>
    body { display: flex; font-family: sans-serif; margin: 0; background: #f4f4f4; }
    .sidebar { width: 25%; background: #fff; padding: 20px; border-right: 1px solid #ccc; height: 100vh; overflow-y: auto; }
    .main { flex: 1; padding: 20px; background: #fff; }
    .preview { width: 25%; background: #f7f7f7; padding: 20px; border-left: 1px solid #ccc; }
    input, select, textarea { width: 100%; padding: 6px; margin-bottom: 12px; }
    .item { padding: 8px; border-bottom: 1px solid #eee; cursor: pointer; }
    .item:hover { background: #efefef; }
    .actions a { margin-right: 10px; text-decoration: none; color: blue; font-size: 13px; }
    .actions a.delete { color: red; }
  </style>
  <script>
    function showPreview(data) {
      document.getElementById('preview').innerHTML = `
        <h3>Customer Details</h3>
        <b>Name:</b> ${data.name}<br>
        <b>Contact:</b> ${data.cno}<br>
        <b>Address:</b> ${data.address}<br>
        <b>Email:</b> ${data.email}<br>
        <b>Reference:</b> ${data.ref} (${data.ref_cno})<br>
        <b>Branch:</b> ${data.branch}<br>
        <b>Status:</b> ${data.status == 1 ? 'Active' : 'Inactive'}<br>
      `;
    }

    function confirmDelete(id) {
      if (confirm("Are you sure you want to delete this customer?")) {
        window.location.href = "?delete=" + id;
      }
    }
  </script>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <h3>Customer List</h3>
  <form method="GET">
    <input type="text" name="search" placeholder="Search customer..." value="<?= htmlspecialchars($search) ?>">
  </form>
  <hr>
  <?php while ($row = $customers->fetch_assoc()): ?>
    <div class="item" onclick='showPreview({
      name: "<?= addslashes($row['Cus_Name']) ?>",
      cno: "<?= $row['Cus_CNo'] ?>",
      email: "<?= addslashes($row['Cus_Email']) ?>",
      address: "<?= addslashes($row['Cus_Address']) ?>",
      ref: "<?= addslashes($row['Cus_Ref']) ?>",
      ref_cno: "<?= $row['Cus_Ref_CNo'] ?>",
      branch: "<?= addslashes($row['Branch_Name']) ?>",
      status: "<?= $row['Customer_Status'] ?>"
    })'>
      <b><?= htmlspecialchars($row['Cus_Name']) ?></b><br>
      <div class="actions">
        <a href="?edit=<?= $row['Cus_Id'] ?>">Edit</a>
        <a href="javascript:void(0);" class="delete" onclick="confirmDelete(<?= $row['Cus_Id'] ?>)">Delete</a>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<!-- Main Form -->
<div class="main">
  <h2><?= $editData ? 'Edit Customer' : 'Add Customer' ?></h2>
  <form method="POST" enctype="multipart/form-data">
    <?php if ($editData): ?>
      <input type="hidden" name="cus_id" value="<?= $editData['Cus_Id'] ?>">
    <?php endif; ?>
    <label>Name:</label>
    <input type="text" name="cus_name" required value="<?= $editData['Cus_Name'] ?? '' ?>">

    <label>Contact Number:</label>
    <input type="number" name="cus_cno" required value="<?= $editData['Cus_CNo'] ?? '' ?>">

    <label>Address:</label>
    <textarea name="cus_address" required><?= $editData['Cus_Address'] ?? '' ?></textarea>

    <label>Email:</label>
    <input type="email" name="cus_email" value="<?= $editData['Cus_Email'] ?? '' ?>">

    <label>Reference Name:</label>
    <input type="text" name="cus_ref" value="<?= $editData['Cus_Ref'] ?? '' ?>">

    <label>Reference Contact No:</label>
    <input type="number" name="cus_ref_cno" value="<?= $editData['Cus_Ref_CNo'] ?? '' ?>">

    <label>Branch:</label>
    <select name="branch_id" required>
      <option value="">-- Select Branch --</option>
      <?php while ($b = $branches->fetch_assoc()): ?>
        <option value="<?= $b['Branch_Id'] ?>" <?= isset($editData['Branch_Id']) && $editData['Branch_Id'] == $b['Branch_Id'] ? 'selected' : '' ?>>
          <?= $b['Branch_Name'] ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label>Status:</label>
    <select name="cus_status">
      <option value="1" <?= (isset($editData['Customer_Status']) && $editData['Customer_Status'] == 1) ? 'selected' : '' ?>>Active</option>
      <option value="0" <?= (isset($editData['Customer_Status']) && $editData['Customer_Status'] == 0) ? 'selected' : '' ?>>Inactive</option>
    </select>

    <label>Customer Photo:</label>
    <input type="file" name="cus_photo" accept="image/*">

    <label>ID Copy:</label>
    <input type="file" name="cus_id_copy" accept="image/*">

    <button type="submit"><?= $editData ? 'Update' : 'Add' ?> Customer</button>
  </form>
</div>

<!-- Preview Panel -->
<div class="preview" id="preview">
  <h3>Customer Details</h3>
  <p>Select a customer to preview</p>
</div>

</body>
</html>
