<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

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

// Fetch staff list
$staffs = $conn->query("
  SELECT s.*, b.Branch_Name 
  FROM Staff_Master s 
  LEFT JOIN Branch_Master b ON s.Branch_Id = b.Branch_Id 
  ORDER BY s.Staff_Id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Staff Master</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="navtop">
  <div class="logo">LOGO</div>
  <h1>Best Mobile Insurance Software</h1>
  <div class="hamburger" onclick="toggleSidebar()">☰</div>
</div>

<div class="container">
  <!-- Sidebar -->
  <aside class="sidebar mobile-hidden" id="sidebarMenu">
    <ul>
      <a href="index.php"><li>Dashboard</li></a>
      <a href="branch.php"><li>Branch Master</li></a>
      <a href="brand.php"><li>Brand Master</li></a>
      <a href="staff.php" class="active"><li>Staff Master</li></a>
      <a href="Customer_Master.php"><li>Customer Master</li></a>
      <a href="add_insurance.php"><li>Insurance Master</li></a>
      <a href="add_defect.php"><li>Defect Master</li></a>
      <a href="insurance_entry.php"><li>Insurance Entry</li></a>
      <a href="serch.php"><li>Claim</li></a>
    </ul>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <div class="form-box">
      <h3><?= $editData ? "Edit Staff" : "Add Staff" ?></h3>
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
          $branches = $conn->query("SELECT Branch_Id, Branch_Name FROM Branch_Master");
          while ($b = $branches->fetch_assoc()):
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

        <button type="submit"><?= $editData ? 'Update Staff' : 'Add Staff' ?></button>
      </form>
    </div>

    <div class="list-box">
      <h3>All Staff</h3>
      <input type="text" class="search-box" placeholder="Search staff..." onkeyup="filterStaff(this.value)">
      <div id="staffList">
        <?php while ($row = $staffs->fetch_assoc()):
          $json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>
          <div class="brand-item" onclick='viewDetails(<?= $json ?>)'>
            <?= htmlspecialchars($row['Staff_Name']) ?>
            <div class="actions">
              <a href="?edit=<?= $row['Staff_Id'] ?>">Edit</a>
              <a href="javascript:void(0)" class="delete" onclick="deleteStaff(<?= $row['Staff_Id'] ?>)">Delete</a>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
      <div id="staffDetails" style="margin-top: 20px;"></div>
    </div>
  </main>
</div>

<script>
function toggleSidebar() {
  document.getElementById('sidebarMenu').classList.toggle('mobile-hidden');
}

function filterStaff(query) {
  const items = document.querySelectorAll('.brand-item');
  items.forEach(item => {
    item.style.display = item.innerText.toLowerCase().includes(query.toLowerCase()) ? 'block' : 'none';
  });
}

function viewDetails(staff) {
  const html = `
    <h4>Staff Details</h4>
    <p><strong>ID:</strong> ${staff.Staff_Id}</p>
    <p><strong>Name:</strong> ${staff.Staff_Name}</p>
    <p><strong>Contact:</strong> ${staff.Staff_CNo}</p>
    <p><strong>Email:</strong> ${staff.Staff_Email}</p>
    <p><strong>Address:</strong> ${staff.Staff_Address}</p>
    <p><strong>Designation:</strong> ${staff.Staff_Designation}</p>
    <p><strong>Branch:</strong> ${staff.Branch_Name || 'Not Assigned'}</p>
    <p><strong>Status:</strong> ${staff.Staff_Status == 1 ? 'Active' : 'Inactive'}</p>
  `;
  document.getElementById("staffDetails").innerHTML = html;
}

function deleteStaff(id) {
  if (confirm("Are you sure you want to delete this staff?")) {
    window.location.href = "?delete=" + id;
  }
}
</script>
</body>
</html>
