<?php
include 'db.php';

$q = $_GET['q'] ?? '';
$result = [];

if ($q) {
  $stmt = $conn->prepare("
    SELECT ie.Insurance_Entry_Id, cm.Cus_Name, ie.Product_Model_Name, ie.IMEI_1 
    FROM Insurance_Entry ie
    JOIN Customer_Master cm ON ie.Cus_Id = cm.Cus_Id
    WHERE cm.Cus_Name LIKE CONCAT('%', ?, '%') OR ie.IMEI_1 LIKE CONCAT('%', ?, '%')
    LIMIT 10
  ");
  $stmt->bind_param("ss", $q, $q);
  $stmt->execute();
  $res = $stmt->get_result();

  while ($row = $res->fetch_assoc()) {
    $result[] = [
      'insurance_entry_id' => $row['Insurance_Entry_Id'],
      'name' => $row['Cus_Name'],
      'model' => $row['Product_Model_Name'],
      'imei1' => $row['IMEI_1'],
    ];
  }
}

header('Content-Type: application/json');
echo json_encode($result);
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
    .result-box button { display: block; width: 100%; margin-bottom: 10px; padding: 10px; background: #eee; border: 1px solid #ccc; cursor: pointer; text-align: left; }
    .result-box button:hover { background: #ddd; }
    .claim-list { margin-top: 40px; }
    .claim-item { padding: 10px; border-bottom: 1px solid #ddd; }
    .claim-item img { max-width: 100px; margin-top: 10px; }
  </style>
</head>
<body>
<div class="container">
  <h2 style="text-align:center;">Claim Entry</h2>

  <label>Search Customer (Name or IMEI)</label>
  <input type="text" id="search" placeholder="Start typing...">
  <div id="resultBox" class="result-box"></div>

  <!-- Claim Entry Form -->
  <form action="insert_claim_entry.php" method="POST" enctype="multipart/form-data" style="display:none;" id="claimForm">
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

  <!-- Show All Claims -->
  <div class="claim-list">
    <h3>All Claim Entries</h3>
    <?php
      $query = "
        SELECT ce.*, cm.Cus_Name, ie.Product_Model_Name, cd.Defect_Name
        FROM Claim_Entry ce
        JOIN Insurance_Entry ie ON ce.Insurance_Entry_Id = ie.Insurance_Entry_Id
        JOIN Customer_Master cm ON ie.Cus_Id = cm.Cus_Id
        JOIN Claim_Defects cd ON ce.Defect_Id = cd.Defect_Id
        ORDER BY ce.Claim_Entry_Id DESC
      ";
      $claims = mysqli_query($conn, $query);
      while ($c = mysqli_fetch_assoc($claims)) {
        echo "<div class='claim-item'>
          <b>{$c['Cus_Name']}</b> - {$c['Product_Model_Name']}<br>
          <b>Defect:</b> {$c['Defect_Name']}<br>
          <b>Remarks:</b> {$c['Remarks']}<br>
          <b>Date:</b> {$c['Created_At']}<br>";
        if (!empty($c['Claim_Image_Path'])) {
          echo "<img src='{$c['Claim_Image_Path']}' alt='Claim Image'>";
        }
        echo "</div>";
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
