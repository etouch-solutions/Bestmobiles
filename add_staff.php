<?php
// Connect DB
$conn = new mysqli("localhost", "root", "", "your_db_name");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Insert logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['staff_name'];
  $cno = $_POST['staff_cno'];
  $email = $_POST['staff_email'];
  $address = $_POST['staff_address'];
  $designation = $_POST['staff_designation'];
  $status = $_POST['staff_status'];

  $stmt = $conn->prepare("INSERT INTO Staff_Master (Staff_Name, Staff_CNo, Staff_Email, Staff_Address, Staff_Designation, Staff_Status) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("sisssi", $name, $cno, $email, $address, $designation, $status);
  $stmt->execute();
  $stmt->close();
  echo "<script>location.href='?added=1'</script>";
}

// Search logic
$search = $_GET['search'] ?? '';
$searchSql = $search ? "WHERE Staff_Name LIKE '%$search%' OR Staff_CNo LIKE '%$search%'" : "";
$staffs = $conn->query("SELECT * FROM Staff_Master $searchSql ORDER BY Staff_Id DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Staff Master</title>
  <style>
    body { font-family: Arial; display: flex; background: #f7f7f7; }
    .sidebar { width: 20%; background: #fff; padding: 20px; border-right: 1px solid #ccc; height: 100vh; overflow-y: auto; }
    .main { flex: 1; padding: 20px; }
    .preview { width: 25%; background: #f2f2f2; padding: 20px; border-left: 1px solid #ccc; }
    input, select, textarea { width: 100%; padding: 6px; margin-top: 5px; margin-bottom: 15px; }
    .staff-item { padding: 5px; cursor: pointer; border-bottom: 1px solid #eee; }
    .staff-item:hover { background: #e0e0e0; }
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
        <b>Status:</b> ${data.status == 1 ? 'Active' : 'Inactive'}
      `;
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
    <div class="staff-item" onclick='showPreview({
      name: "<?= addslashes($row['Staff_Name']) ?>",
      cno: "<?= $row['Staff_CNo'] ?>",
      email: "<?= $row['Staff_Email'] ?>",
      address: "<?= addslashes($row['Staff_Address']) ?>",
      designation: "<?= addslashes($row['Staff_Designation']) ?>",
      status: "<?= $row['Staff_Status'] ?>"
    })'>
      <?= $row['Staff_Name'] ?>
    </div>
  <?php endwhile; ?>
</div>

<!-- Main Form -->
<div class="main">
  <h2>Add Staff</h2>
  <form method="POST">
    <label>Name:</label>
    <input type="text" name="staff_name" required>

    <label>Contact No:</label>
    <input type="number" name="staff_cno" required>

    <label>Email:</label>
    <input type="email" name="staff_email" required>

    <label>Address:</label>
    <textarea name="staff_address" required></textarea>

    <label>Designation:</label>
    <input type="text" name="staff_designation" required>

    <label>Status:</label>
    <select name="staff_status">
      <option value="1">Active</option>
      <option value="0">Inactive</option>
    </select>

    <input type="submit" value="Add Staff">
  </form>
</div>

<!-- Preview Panel -->
<div class="preview" id="preview">
  <h3>Staff Details</h3>
  <p>Select a staff from the left to view details.</p>
</div>

</body>
</html>
