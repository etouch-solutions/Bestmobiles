<?php
include 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insurance_entry_id'])) {
    $insurance_id = $_POST['insurance_entry_id'];
    $defect_id = $_POST['defect_id'];
    $remarks = $_POST['remarks'];
    
    // Handle image upload
    $imgPath = '';
    if ($_FILES['claim_image']['name']) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir);
        $imgName = time() . '_' . basename($_FILES['claim_image']['name']);
        $imgPath = $uploadDir . $imgName;
        move_uploaded_file($_FILES['claim_image']['tmp_name'], $imgPath);
    }

    $stmt = $conn->prepare("INSERT INTO Claim_Entry (Insurance_Entry_Id, Defect_Id, Remarks, Claim_Photo_Path, Created_At) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiss", $insurance_id, $defect_id, $remarks, $imgPath);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Claim submitted!');location.href='claim_entry.php';</script>";
}

// Fetch defects
$defects = $conn->query("SELECT Defect_Id, Defect_Name FROM Claim_Defects");

// Fetch previous claims
$claims = $conn->query("
    SELECT c.*, d.Defect_Name, cu.Cus_Name, i.Product_Model_Name 
    FROM Claim_Entry c
    LEFT JOIN Claim_Defects d ON c.Defect_Id = d.Defect_Id
    LEFT JOIN Insurance_Entry i ON c.Insurance_Entry_Id = i.Insurance_Entry_Id
    LEFT JOIN Customer_Master cu ON i.Cus_Id = cu.Cus_Id
    ORDER BY c.Claim_Id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Claim Entry</title>
  <style>
    body { font-family: Arial; background: #f5f5f5; padding: 50px; }
    .container { max-width: 700px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
    input, select, textarea { width: 100%; padding: 10px; margin-top: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px; }
    button { background: green; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
    .result-box { background: #f0f0f0; padding: 10px; margin-bottom: 20px; }
    .claim-item { border-bottom: 1px solid #ccc; padding: 10px 0; }
    .claim-item img { width: 100px; vertical-align: middle; border-radius: 5px; }
  </style>
</head>
<body>
<div class="container">
  <h2 style="text-align:center;">Claim Entry</h2>

  <label>Search Customer (Name or IMEI)</label>
  <input type="text" id="search" placeholder="Start typing...">
  <div id="resultBox" class="result-box"></div>

  <form action="" method="POST" enctype="multipart/form-data" style="display:none;" id="claimForm">
    <input type="hidden" name="insurance_entry_id" id="insurance_entry_id">

    <label>Select Defect</label>
    <select name="defect_id" required>
      <option value="">-- Select Defect --</option>
      <?php while ($row = $defects->fetch_assoc()): ?>
        <option value="<?= $row['Defect_Id'] ?>"><?= $row['Defect_Name'] ?></option>
      <?php endwhile; ?>
    </select>

    <label>Remarks (optional)</label>
    <textarea name="remarks" rows="3"></textarea>

    <label>Upload Product Image</label>
    <input type="file" name="claim_image" accept="image/*" required>

    <button type="submit">Submit Claim</button>
  </form>

  <h3>Previous Claims</h3>
  <?php while ($c = $claims->fetch_assoc()): ?>
    <div class="claim-item">
      <b><?= htmlspecialchars($c['Cus_Name']) ?></b> | <?= $c['Product_Model_Name'] ?> <br>
      <b>Defect:</b> <?= $c['Defect_Name'] ?> <br>
      <b>Remarks:</b> <?= $c['Remarks'] ?> <br>
      <?php if ($c['Claim_Photo_Path']): ?>
        <img src="<?= $c['Claim_Photo_Path'] ?>" alt="Claim Photo">
      <?php endif; ?>
      <div><small><?= $c['Created_At'] ?></small></div>
    </div>
  <?php endwhile; ?>
</div>

<script>
document.getElementById('search').addEventListener('input', function () {
  const query = this.value;
  if (query.length < 2) return;

  fetch('search_insurance_entry.php?q=' + encodeURIComponent(query))
    .then(res => res.json())
    .then(data => {
      const box = document.getElementById('resultBox');
      box.innerHTML = '';
      if (data.length > 0) {
        data.forEach(item => {
          const btn = document.createElement('button');
          btn.textContent = `${item.name} - ${item.model} - IMEI: ${item.imei1}`;
          btn.onclick = () => {
            document.getElementById('insurance_entry_id').value = item.insurance_entry_id;
            document.getElementById('claimForm').style.display = 'block';
            box.innerHTML = `<b>Selected:</b> ${item.name} (${item.model})`;
          };
          box.appendChild(btn);
        });
      } else {
        box.innerHTML = "No results.";
      }
    });
});
</script>
</body>
</html>
