<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

// Insert
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['insurance_name'];
  $desc = $_POST['insurance_description'];
  $percent = $_POST['premium_percentage'];
  $duration = $_POST['duration'];
  $status = $_POST['insurance_status'];

  $stmt = $conn->prepare("INSERT INTO Insurance_Master (Insurance_Name, Insurance_Description, Premium_Percentage, Duration, Insurance_Status) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("ssisi", $name, $desc, $percent, $duration, $status);
  $stmt->execute();
  $stmt->close();
  echo "<script>location.href='?added=1'</script>";
}

// Search logic
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
  </style>
  <script>
    function showPreview(data) {
      document.getElementById('preview').innerHTML = `
        <h3>Insurance Plan Details</h3>
        <b>Name:</b> ${data.name}<br>
        <b>Description:</b> ${data.desc}<br>
        <b>Premium %:</b> ${data.percent}%<br>
        <b>Duration:</b> ${data.duration}<br>
        <b>Status:</b> ${data.status == 1 ? 'Active' : 'Inactive'}
      `;
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
      $name = addslashes($row['Insurance_Name']);
      $desc = addslashes($row['Insurance_Description']);
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
      <?= htmlspecialchars($row['Insurance_Name']) ?>
    </div>
  <?php endwhile; ?>
</div>

<!-- Main -->
<div class="main">
  <h2>Add Insurance Plan</h2>
  <form method="POST">
    <label>Insurance Name:</label>
    <input type="text" name="insurance_name" required>

    <label>Description:</label>
    <textarea name="insurance_description" required></textarea>

    <label>Premium Percentage (%):</label>
    <input type="number" name="premium_percentage" min="1" required>

    <label>Duration (in Months):</label>
    <select name="duration" required>
      <option value="">-- Select Duration --</option>
      <?php for ($i = 1; $i <= 24; $i++): ?>
        <option value="<?= $i ?>"><?= $i ?> Month<?= $i > 1 ? 's' : '' ?></option>
      <?php endfor; ?>
    </select>

    <label>Status:</label>
    <select name="insurance_status">
      <option value="1">Active</option>
      <option value="0">Inactive</option>
    </select>

    <input type="submit" value="Add Insurance">
  </form>
</div>

<!-- Preview -->
<div class="preview" id="preview">
  <h3>Insurance Plan Details</h3>
  <p>Select a plan to view details.</p>
</div>

</body>
</html>
