<?php
include 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Insert logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insurance_entry_id'])) {
    $insurance_id = $_POST['insurance_entry_id'];
    $defect_id = $_POST['defect_id'];
    $remarks = $_POST['remarks'];
    $img_name = $_FILES['claim_image']['name'];
    $tmp_name = $_FILES['claim_image']['tmp_name'];

    $upload_path = "uploads/claims/";
    if (!file_exists($upload_path)) {
        mkdir($upload_path, 0777, true);
    }
    $file_path = $upload_path . time() . '_' . basename($img_name);
    move_uploaded_file($tmp_name, $file_path);

    $stmt = $conn->prepare("INSERT INTO Claim_Entry (Insurance_Entry_Id, Defect_Id, Remarks, Claim_Image_Path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $insurance_id, $defect_id, $remarks, $file_path);
    $stmt->execute();
    $stmt->close();
    header("Location: Claim_Entry.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Claim Entry</title>
  <style>
    body { font-family: Arial; background: #f5f5f5; padding: 50px; }
    .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
    input, select, textarea { width: 100%; padding: 10px; margin-top: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px; }
    button { background: green; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
    .result-box { background: #f0f0f0; padding: 10px; margin-bottom: 20px; }
    .claim-list { margin-top: 40px; }
    .claim-item { background: #f9f9f9; border-left: 4px solid #007BFF; padding: 10px; margin-bottom: 10px; }
  </style>
</head>
<body>
<div class="container">
  <h2 style="text-align:center;">Claim Entry</h2>

  <label>Search Customer (Name or IMEI)</label>
  <input type="text" id="search" placeholder="Start typing...">
  <div id="resultBox" class="result-box"></div>

  <form method="POST" enctype="multipart/form-data" style="display:none;" id="claimForm">
    <input type="hidden" name="insurance_entry_id" id="insurance_entry_id">

    <label>Select Defect</label>
    <select name="defect_id" required>
      <option value="">-- Select Defect --</option>
      <?php
        $res = mysqli_query($conn, "SELECT Defect_Id, Defect_Name FROM Claim_Defects");
        while ($row = mysqli_fetch_assoc($res)) {
          echo "<option value='{$row['Defect_Id']}'>{$row['Defect_Name']}</option>";
        }
      ?>
    </select>

    <label>Remarks (optional)</label>
    <textarea name="remarks"></textarea>

    <label>Upload Product Image</label>
    <input type="file" name="claim_image" accept="image/*" required>

    <button type="submit">Submit Claim</button>
  </form>

  <div class="claim-list">
    <h3>All Claims</h3>
    <?php
      $claims = mysqli_query($conn, "
        SELECT c.*, d.Defect_Name, i.Product_Model_Name, cu.Cus_Name 
        FROM Claim_Entry c
        JOIN Claim_Defects d ON c.Defect_Id = d.Defect_Id
        JOIN Insurance_Entry i ON i.Insurance_Entry_Id = c.Insurance_Entry_Id
        JOIN Customer_Master cu ON cu.Cus_Id = i.Cus_Id
        ORDER BY c.Claim_Id DESC
      ");
      while ($claim = mysqli_fetch_assoc($claims)) {
        echo "<div class='claim-item'>
          <strong>Customer:</strong> {$claim['Cus_Name']}<br>
          <strong>Model:</strong> {$claim['Product_Model_Name']}<br>
          <strong>Defect:</strong> {$claim['Defect_Name']}<br>
          <strong>Remarks:</strong> {$claim['Remarks']}<br>
          <strong>Date:</strong> {$claim['Created_At']}<br>
          <img src='{$claim['Claim_Image_Path']}' width='100' style='margin-top:10px; border-radius:5px;'>
        </div>";
      }
    ?>
  </div>
</div>

<script>
document.getElementById('search').addEventListener('input', function () {
  const query = this.value;
  if (query.length < 2) return;

  fetch(`search_insurance_entry.php?q=${query}`)
    .then(res => res.json())
    .then(data => {
      const box = document.getElementById('resultBox');
      box.innerHTML = '';
      if (data.length > 0) {
        data.forEach(item => {
          const btn = document.createElement('button');
          btn.innerText = `${item.name} - ${item.model} - IMEI: ${item.imei1}`;
          btn.onclick = () => {
            document.getElementById('insurance_entry_id').value = item.insurance_entry_id;
            document.getElementById('claimForm').style.display = 'block';
            box.innerHTML = `<b>Selected:</b> ${item.name} (${item.model})`;
          };
          box.appendChild(btn);
        });
      } else {
        box.innerText = "No matching customers found.";
      }
    });
});
</script>
</body>
</html>
