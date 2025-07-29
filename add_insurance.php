<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

// INSERT or UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['insurance_id'] ?? null;
    $name = $_POST['insurance_name'];
    $desc = $_POST['insurance_description'];
    $percent = $_POST['premium_percentage'];
    $duration = $_POST['duration'];
    $status = $_POST['insurance_status'];

    if ($id) {
        // Update
        $stmt = $conn->prepare("UPDATE Insurance_Master SET Insurance_Name=?, Insurance_Description=?, Premium_Percentage=?, Duration=?, Insurance_Status=? WHERE Insurance_Id=?");
        $stmt->bind_param("ssiiii", $name, $desc, $percent, $duration, $status, $id);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO Insurance_Master (Insurance_Name, Insurance_Description, Premium_Percentage, Duration, Insurance_Status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiii", $name, $desc, $percent, $duration, $status);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?saved=1");
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM Insurance_Master WHERE Insurance_Id = $id");
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?deleted=1");
    exit;
}

// Fetch for edit
$editData = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM Insurance_Master WHERE Insurance_Id = $editId");
    if ($res && $res->num_rows > 0) {
        $editData = $res->fetch_assoc();
    }
}

// Search and fetch all
$search = $_GET['search'] ?? '';
$searchSql = $search ? "WHERE Insurance_Name LIKE '%$search%'" : "";
$plans = $conn->query("SELECT * FROM Insurance_Master $searchSql ORDER BY Insurance_Id DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Insurance Plan Master</title>
  <style>
    body { display: flex; font-family: sans-serif; margin: 0; }
    .sidebar { width: 25%; background: #fff; padding: 20px; border-right: 1px solid #ccc; height: 100vh; overflow-y: auto; }
    .main { flex: 1; padding: 20px; background: #f4f4f4; }
    .preview { width: 25%; padding: 20px; background: #fafafa; border-left: 1px solid #ccc; }
    .plan-item { padding: 8px; cursor: pointer; border-bottom: 1px solid #eee; }
    .plan-item:hover { background: #e6e6e6; }
    input, textarea, select { width: 100%; padding: 6px; margin-bottom: 10px; }
    .actions { font-size: 12px; margin-top: 4px; }
    .actions a { margin-right: 10px; color: blue; text-decoration: none; }
    .actions a.delete { color: red; }
  </style>
  <script>
    function showPreview(data) {
      document.getElementById('preview').innerHTML = `
        <h3>Insurance Plan Details</h3>
        <b>Name:</b> ${data.name}<br>
        <b>Description:</b> ${data.desc}<br>
        <b>Premium %:</b> ${data.percent}%<br>
        <b>Duration:</b> ${data.duration} Month(s)<br>
        <b>Status:</b> ${data.status == 1 ? 'Active' : 'Inactive'}
      `;
    }

    function confirmDelete(id) {
      if (confirm("Are you sure you want to delete this insurance plan?")) {
        window.location.href = "?delete=" + id;
      }
    }
  </script>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <h3>Insurance Plans</h3>
  <form method="get">
    <input type="text" name="search" placeholder="Search plan..." value="<?= htmlspecialchars($search) ?>">
  </form>
  <hr>
  <?php while ($row = $plans->fetch_assoc()): ?>
    <?php
      $id = $row['Insurance_Id'];
      $name = htmlspecialchars($row['Insurance_Name'], ENT_QUOTES);
      $desc = htmlspecialchars($row['Insurance_Description'], ENT_QUOTES);
      $percent = $row['Premium_Percentage'];
      $duration = $row['Duration'];
      $status = $row['Insurance_Status'];
    ?>
    <div class="plan-item"
         onclick='showPreview({
           name: "<?= $name ?>",
           desc: "<?= $desc ?>",
           percent: "<?= $percent ?>",
           duration: "<?= $duration ?>",
           status: "<?= $status ?>"
         })'>
      <b><?= $name ?></b>
      <div class="actions">
        <a href="?edit=<?= $id ?>">Edit</a>
        <a href="javascript:void(0)" class="delete" onclick="confirmDelete(<?= $id ?>)">Delete</a>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<!-- Main -->
<div class="main">
  <h2><?= $editData ? "Edit Insurance Plan" : "Add Insurance Plan" ?></h2>
  <form method="POST">
    <?php if ($editData): ?>
      <input type="hidden" name="insurance_id" value="<?= $editData['Insurance_Id'] ?>">
    <?php endif; ?>

    <label>Insurance Name:</label>
    <input type="text" name="insurance_name" required value="<?= $editData['Insurance_Name'] ?? '' ?>">

    <label>Description:</label>
    <textarea name="insurance_description" required><?= $editData['Insurance_Description'] ?? '' ?></textarea>

    <label>Premium Percentage (%):</label>
    <input type="number" name="premium_percentage" min="1" required value="<?= $editData['Premium_Percentage'] ?? '' ?>">

    <label>Duration (in Months):</label>
    <select name="duration" required>
      <option value="">-- Select Duration --</option>
      <?php for ($i = 1; $i <= 24; $i++): ?>
        <option value="<?= $i ?>" <?= (isset($editData['Duration']) && $editData['Duration'] == $i) ? 'selected' : '' ?>>
          <?= $i ?> Month<?= $i > 1 ? 's' : '' ?>
        </option>
      <?php endfor; ?>
    </select>

    <label>Status:</label>
    <select name="insurance_status">
      <option value="1" <?= (isset($editData['Insurance_Status']) && $editData['Insurance_Status'] == 1) ? 'selected' : '' ?>>Active</option>
      <option value="0" <?= (isset($editData['Insurance_Status']) && $editData['Insurance_Status'] == 0) ? 'selected' : '' ?>>Inactive</option>
    </select>

    <input type="submit" value="<?= $editData ? 'Update Plan' : 'Add Insurance' ?>">
  </form>
</div>

<!-- Preview -->
<div class="preview" id="preview">
  <h3>Insurance Plan Details</h3>
  <p>Select a plan to view details.</p>
</div>

</body>
</html>
