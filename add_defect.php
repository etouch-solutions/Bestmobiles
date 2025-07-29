<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php'; // $conn should be your mysqli connection

// INSERT or UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['defect_id'] ?? null;
    $name = $_POST['defect_name'] ?? '';
    $desc = $_POST['defect_description'] ?? '';
    $status = $_POST['defect_status'] ?? 1;

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE Defect_Master SET Defect_Name=?, Defect_Description=?, Is_Active=?, Updated_At=NOW() WHERE Defect_Id=?");
        $stmt->bind_param("ssii", $name, $desc, $status, $id);
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO Defect_Master (Defect_Name, Defect_Description, Is_Active) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $desc, $status);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: add_defect.php?added=1");
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM Defect_Master WHERE Defect_Id = $id");
    header("Location: add_defect.php?deleted=1");
    exit;
}

// GET for edit
$editData = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM Defect_Master WHERE Defect_Id = $editId");
    if ($res && $res->num_rows > 0) {
        $editData = $res->fetch_assoc();
    }
}

// FETCH ALL
$search = $_GET['search'] ?? '';
$searchSql = $search ? "WHERE Defect_Name LIKE '%$search%' OR Defect_Description LIKE '%$search%'" : "";
$defects = $conn->query("SELECT * FROM Defect_Master $searchSql ORDER BY Defect_Id DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Defect Master</title>
  <style>
    body { font-family: Arial, sans-serif; display: flex; background: #f9f9f9; margin: 0; }
    .sidebar { width: 25%; background: #fff; padding: 20px; border-right: 1px solid #ccc; height: 100vh; overflow-y: auto; }
    .main { width: 50%; padding: 20px; }
    .preview { width: 25%; background: #f0f0f0; padding: 20px; border-left: 1px solid #ccc; }
    input, select, textarea {
      width: 100%; padding: 8px; margin-top: 5px; margin-bottom: 15px;
      border: 1px solid #ccc; border-radius: 4px;
    }
    .item {
      padding: 10px; cursor: pointer;
      border-bottom: 1px solid #eee;
    }
    .item:hover { background: #e0e0e0; }
    .actions a { margin-right: 10px; color: blue; text-decoration: none; }
    .actions a.delete { color: red; }
  </style>
  <script>
    function showPreview(data) {
      document.getElementById('preview').innerHTML = `
        <h3>Defect Details</h3>
        <p><strong>Name:</strong> ${data.name}</p>
        <p><strong>Description:</strong> ${data.desc}</p>
        <p><strong>Status:</strong> ${data.status == 1 ? 'Active' : 'Inactive'}</p>
      `;
    }
    function confirmDelete(id) {
      if (confirm("Are you sure you want to delete this defect?")) {
        window.location.href = "?delete=" + id;
      }
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
      $id = $row['Defect_Id'];
      $name = htmlspecialchars($row['Defect_Name'], ENT_QUOTES);
      $desc = htmlspecialchars($row['Defect_Description'], ENT_QUOTES);
      $status = $row['Is_Active'];
    ?>
    <div class="item" onclick='showPreview({ name: "<?= $name ?>", desc: "<?= $desc ?>", status: <?= $status ?> })'>
      <b><?= $name ?></b><br>
      <div class="actions">
        <a href="?edit=<?= $id ?>">Edit</a>
        <a href="javascript:void(0)" class="delete" onclick="confirmDelete(<?= $id ?>)">Delete</a>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<!-- Form -->
<div class="main">
  <h2><?= $editData ? "Edit Defect" : "Add Defect Type" ?></h2>
  <form method="POST">
    <?php if ($editData): ?>
      <input type="hidden" name="defect_id" value="<?= $editData['Defect_Id'] ?>">
    <?php endif; ?>

    <label>Defect Name:</label>
    <input type="text" name="defect_name" required value="<?= $editData['Defect_Name'] ?? '' ?>">

    <label>Defect Description:</label>
    <textarea name="defect_description" required><?= $editData['Defect_Description'] ?? '' ?></textarea>

    <label>Status:</label>
    <select name="defect_status" required>
      <option value="1" <?= (isset($editData['Is_Active']) && $editData['Is_Active'] == 1) ? 'selected' : '' ?>>Active</option>
      <option value="0" <?= (isset($editData['Is_Active']) && $editData['Is_Active'] == 0) ? 'selected' : '' ?>>Inactive</option>
    </select>

    <input type="submit" value="<?= $editData ? 'Update Defect' : 'Add Defect' ?>">
  </form>
</div>

<!-- Preview Panel -->
<div class="preview" id="preview">
  <h3>Defect Details</h3>
  <p>Select a defect to preview.</p>
</div>

</body>
</html>
