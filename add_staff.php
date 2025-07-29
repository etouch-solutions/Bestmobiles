<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

// Get branches for dropdown
$branches = $conn->query("SELECT Branch_Id, Branch_Name FROM Branch_Master");

// Insert or Update logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = $_POST['staff_id'] ?? null;
  $name = $_POST['staff_name'];
  $cno = $_POST['staff_cno'];
  $email = $_POST['staff_email'];
  $address = $_POST['staff_address'];
  $designation = $_POST['staff_designation'];
  $status = $_POST['staff_status'];
  $branch_id = $_POST['branch_id'];

  if ($id) {
    $stmt = $conn->prepare("UPDATE Staff_Master SET Staff_Name=?, Staff_CNo=?, Staff_Email=?, Staff_Address=?, Staff_Designation=?, Staff_Status=?, Branch_Id=? WHERE Staff_Id=?");
    $stmt->bind_param("sisssiii", $name, $cno, $email, $address, $designation, $status, $branch_id, $id);
  } else {
    $stmt = $conn->prepare("INSERT INTO Staff_Master (Staff_Name, Staff_CNo, Staff_Email, Staff_Address, Staff_Designation, Staff_Status, Branch_Id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssii", $name, $cno, $email, $address, $designation, $status, $branch_id);
  }

  $stmt->execute();
  $stmt->close();
  header("Location: staff.php");
  exit();
}

// Delete
if (isset($_GET['delete'])) {
  $delId = intval($_GET['delete']);
  $conn->query("DELETE FROM Staff_Master WHERE Staff_Id = $delId");
  header("Location: staff.php?deleted=1");
  exit();
}

// Fetch for edit
$editData = null;
if (isset($_GET['edit'])) {
  $editId = intval($_GET['edit']);
  $res = $conn->query("SELECT * FROM Staff_Master WHERE Staff_Id = $editId");
  if ($res && $res->num_rows > 0) {
    $editData = $res->fetch_assoc();
  }
}

// Search logic
$search = $_GET['search'] ?? '';
$searchSql = $search ? "WHERE s.Staff_Name LIKE '%$search%' OR s.Staff_CNo LIKE '%$search%'" : "";

// Fetch all staff with branch name
$staffs = $conn->query("
  SELECT s.*, b.Branch_Name 
  FROM Staff_Master s 
  LEFT JOIN Branch_Master b ON s.Branch_Id = b.Branch_Id 
  $searchSql 
  ORDER BY s.Staff_Id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Staff Master with Branch</title>
  <style>
    body { font-family: Arial; display: flex; background: #f7f7f7; margin: 0; }
    .sidebar { width: 25%; background: #fff; padding: 20px; border-right: 1px solid #ccc; height: 100vh; overflow-y: auto; }
    .main { flex: 1; padding: 20px; }
    .preview { width: 25%; background: #f2f2f2; padding: 20px; border-left: 1px solid #ccc; }
    input, select, textarea { width: 100%; padding: 6px; margin-top: 5px; margin-bottom: 15px; }
    .staff-item { padding: 8px; cursor: pointer; border-bottom: 1px solid #eee; }
    .staff-item:hover { background: #e0e0e0; }
    .actions { font-size: 12px; margin-top: 4px; }
    .actions a { margin-right: 10px; text-decoration: none; color: blue; }
    .actions a.delete { color: red; }
  </style>
  <script>
    function showPreview(data) {
      document.getElementById('preview').innerHTML = `
        <h3>Staff Details</h3>
        <b>Name:</b> ${data.name}<br>
        <b>Contact:</b> ${data.cno}<br>
        <b>Email:</b> ${data.email}<br>
        <b>Address:</b> ${data.address}<br>
        <b>Designation:</b> ${data.designation}<br>
        <b>Branch:</b> ${data.branch}<br>
        <b>Status:</b> ${data.status == 1 ? 'Active' : 'Inactive'}
      `;
    }

    function deleteStaff(id) {
      if (confirm("Are you sure you want to delete this staff?")) {
        window.location.href = "?delete=" + id;
      }
    }
  </script>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <h3>Staff List</h3>
  <form method="get">
    <input type="text" name="search" placeholder="Search staff..." value="<?= htmlspecialchars($search) ?>">
  </form>
  <hr>
  <?php while ($row = $staffs->fetch_assoc()): ?>
    <?php
      $id = $row['Staff_Id'];
      $name = addslashes($row['Staff_Name']);
      $cno = $row['Staff_CNo'];
      $email = addslashes($row['Staff_Email']);
      $address = addslashes($row['Staff_Address']);
      $designation = addslashes($row['Staff_Designation']);
      $status = $row['Staff_Status'];
      $branch = addslashes($row['Branch_Name'] ?? 'Not Assigned');
    ?>
    <div class="staff-item" onclick='showPreview({
      name: "<?= $name ?>",
      cno: "<?= $cno ?>",
      email: "<?= $email ?>",
      address: "<?= $address ?>",
      designation: "<?= $designation ?>",
      status: "<?= $status ?>",
      branch: "<?= $branch ?>"
    })'>
      <b><?= htmlspecialchars($row['Staff_Name']) ?></b>
      <div class="actions">
        <a href="?edit=<?= $id ?>">Edit</a>
        <a href="javascript:void(0)" class="delete" onclick="deleteStaff(<?= $id ?>)">Delete</a>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<!-- Main Form -->
<div class="main">
  <h2><?= $editData ? "Edit Staff" : "Add Staff" ?></h2>
  <form method="POST">
    <?php if ($editData): ?>
      <input type="hidden" name="staff_id" value="<?= $editData['Staff_Id'] ?>">
    <?php endif; ?>

    <label>Name:</label>
    <input type="text" name="staff_name" required value="<?= $editData['Staff_Name'] ?? '' ?>">

    <label>Contact No:</label>
    <input type="number" name="staff_cno" required value="<?= $editData['Staff_CNo'] ?? '' ?>">

    <label>Email:</label>
    <input type="email" name="staff_email" required value="<?= $editData['Staff_Email'] ?? '' ?>">

    <label>Address:</label>
    <textarea name="staff_address" required><?= $editData['Staff_Address'] ?? '' ?></textarea>

    <label>Designation:</label>
    <input type="text" name="staff_designation" required value="<?= $editData['Staff_Designation'] ?? '' ?>">

    <label>Branch:</label>
    <select name="branch_id" required>
      <option value="">-- Select Branch --</option>
      <?php
      $branchList = $conn->query("SELECT Branch_Id, Branch_Name FROM Branch_Master");
      while ($b = $branchList->fetch_assoc()):
        $selected = (isset($editData['Branch_Id']) && $editData['Branch_Id'] == $b['Branch_Id']) ? 'selected' : '';
      ?>
        <option value="<?= $b['Branch_Id'] ?>" <?= $selected ?>><?= htmlspecialchars($b['Branch_Name']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Status:</label>
    <select name="staff_status">
      <option value="1" <?= (isset($editData['Staff_Status']) && $editData['Staff_Status'] == 1) ? 'selected' : '' ?>>Active</option>
      <option value="0" <?= (isset($editData['Staff_Status']) && $editData['Staff_Status'] == 0) ? 'selected' : '' ?>>Inactive</option>
    </select>

    <input type="submit" value="<?= $editData ? 'Update Staff' : 'Add Staff' ?>">
  </form>
</div>

<!-- Preview -->
<div class="preview" id="preview">
  <h3>Staff Details</h3>
  <p>Select a staff from the left to view details.</p>
</div>

</body>
</html>
