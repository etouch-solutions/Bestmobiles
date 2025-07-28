<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';
<<<<<<< HEAD
// DB Connect
$conn = new mysqli("localhost", "root", "", "your_db_name");
if ($conn->connect_error) die("DB Connection failed: " . $conn->connect_error);
=======
>>>>>>> 69c4bf2 (Initial push of full Bestmobiles project)

// Insert Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['defect_name'];
  $desc = $_POST['defect_description'];
  $status = $_POST['defect_status'];

  $stmt = $conn->prepare("INSERT INTO Claim_Defects (Defect_Name, Defect_Description, Defect_Status) VALUES (?, ?, ?)");
  $stmt->bind_param("ssi", $name, $desc, $status);
  $stmt->execute();
  $stmt->close();
  echo "<script>location.href='?added=1'</script>";
}

// Fetch Defects
$search = $_GET['search'] ?? '';
$searchSql = $search ? "WHERE Defect_Name LIKE '%$search%' OR Defect_Description LIKE '%$search%'" : "";
$defects = $conn->query("SELECT * FROM Claim_Defects $searchSql ORDER BY Defect_Id DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Defect Master</title>
  <style>
<<<<<<< HEAD
    body { font-family: Arial; display: flex; background: #f9f9f9; }
    .sidebar { width: 20%; background: #fff; padding: 20px; border-right: 1px solid #ccc; height: 100vh; overflow-y: auto; }
    .main { flex: 1; padding: 20px; }
    .preview { width: 25%; background: #f0f0f0; padding: 20px; border-left: 1px solid #ccc; }
    input, select, textarea { width: 100%; padding: 6px; margin-top: 5px; margin-bottom: 15px; }
    .item { padding: 5px; cursor: pointer; border-bottom: 1px solid #eee; }
    .item:hover { background: #e0e0e0; }
=======
    body { font-family: Arial; display: flex; background: #f9f9f9; margin: 0; }
    .sidebar { width: 25%; background: #fff; padding: 20px; border-right: 1px solid #ccc; height: 100vh; overflow-y: auto; }
    .main { width: 50%; padding: 20px; }
    .preview { width: 25%; background: #f0f0f0; padding: 20px; border-left: 1px solid #ccc; }
    input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; }
    .item { padding: 10px; cursor: pointer; border-bottom: 1px solid #eee; }
    .item:hover { background: #e0e0e0; }
    h2, h3 { margin-top: 0; }
>>>>>>> 69c4bf2 (Initial push of full Bestmobiles project)
  </style>
  <script>
    function showPreview(data) {
      document.getElementById('preview').innerHTML = `
        <h3>Defect Details</h3>
<<<<<<< HEAD
        <b>Name:</b> ${data.name}<br>
        <b>Description:</b> ${data.desc}<br>
        <b>Status:</b> ${data.status == 1 ? 'Active' : 'Inactive'}
=======
        <p><strong>Name:</strong> ${data.name}</p>
        <p><strong>Description:</strong> ${data.desc}</p>
        <p><strong>Status:</strong> ${data.status == 1 ? 'Active' : 'Inactive'}</p>
>>>>>>> 69c4bf2 (Initial push of full Bestmobiles project)
      `;
    }
  </script>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <h3>Defect Types</h3>
  <form method="get">
    <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
  </form>
  <hr>
  <?php while ($row = $defects->fetch_assoc()): ?>
    <?php
<<<<<<< HEAD
      $name = addslashes($row['Defect_Name']);
      $desc = addslashes($row['Defect_Description']);
      $status = $row['Defect_Status'];
    ?>
    <div class="item" onclick='showPreview({
      name: "<?= $name ?>",
      desc: "<?= $desc ?>",
      status: "<?= $status ?>"
    })'>
      <?= htmlspecialchars($row['Defect_Name']) ?>
=======
      $name = htmlspecialchars($row['Defect_Name']);
      $desc = htmlspecialchars($row['Defect_Description']);
      $status = $row['Defect_Status'];
    ?>
    <div class="item" onclick='showPreview({
      name: "<?= addslashes($name) ?>",
      desc: "<?= addslashes($desc) ?>",
      status: "<?= $status ?>"
    })'>
      <?= $name ?>
>>>>>>> 69c4bf2 (Initial push of full Bestmobiles project)
    </div>
  <?php endwhile; ?>
</div>

<<<<<<< HEAD
<!-- Main Form -->
=======
<!-- Form Section -->
>>>>>>> 69c4bf2 (Initial push of full Bestmobiles project)
<div class="main">
  <h2>Add Defect Type</h2>
  <form method="POST">
    <label>Defect Name:</label>
    <input type="text" name="defect_name" required>

    <label>Defect Description:</label>
    <textarea name="defect_description" required></textarea>

    <label>Status:</label>
    <select name="defect_status">
      <option value="1">Active</option>
      <option value="0">Inactive</option>
    </select>

    <input type="submit" value="Add Defect">
  </form>
</div>

<!-- Preview Section -->
<div class="preview" id="preview">
  <h3>Defect Details</h3>
  <p>Select a defect to preview.</p>
</div>

</body>
</html>
