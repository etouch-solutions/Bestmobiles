<?php
include 'db.php';

// Handle AJAX search
if (isset($_GET['q'])) {
  $q = $_GET['q'];
  $data = [];

  $res = mysqli_query($conn, "
    SELECT 
      i.Insurance_Entry_Id,
      c.Cus_Name AS name,
      i.Product_Model_Name AS model,
      i.IMEI_1 AS imei1,
      i.Product_Value AS product_value,
      i.Premium_Amount AS premium_amount,
      (SELECT COUNT(*) FROM Claim_Entry ce WHERE ce.Insurance_Entry_Id = i.Insurance_Entry_Id) AS total_claims,
      (SELECT IFNULL(SUM(cd.Defect_Value), 0)
       FROM Claim_Entry ce
       JOIN Claim_Defects cd ON ce.Defect_Id = cd.Defect_Id
       WHERE ce.Insurance_Entry_Id = i.Insurance_Entry_Id) AS total_claimed
    FROM Insurance_Entry i
    JOIN Customer_Master c ON i.Cus_Id = c.Cus_Id
    WHERE c.Cus_Name LIKE '%$q%' OR c.Cus_CNo LIKE '%$q%' OR i.IMEI_1 LIKE '%$q%' 
  ");

  while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
  }
  header('Content-Type: application/json');
  echo json_encode($data);
  exit;
}
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
    .info-box { background: #f9f9f9; border: 1px solid #ccc; padding: 10px; margin-top: 10px; }
  </style>
</head>
<body>
<div class="container">
  <h2 style="text-align:center;">Claim Entry</h2>

  <label>Search Customer (Name / Phone / IMEI)</label>
  <input type="text" id="search" placeholder="Start typing...">
  <div id="resultBox" class="result-box"></div>

  <form action="insert_claim_entry.php" method="POST" enctype="multipart/form-data" style="display:none;" id="claimForm">
    <input type="hidden" name="insurance_entry_id" id="insurance_entry_id">

    <div id="infoBox" class="info-box"></div>

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
</div>

<script>
document.getElementById('search').addEventListener('input', function () {
  const query = this.value.trim();
  if (query.length < 2) return;

  fetch(`?q=${encodeURIComponent(query)}`)
    .then(res => res.json())
    .then(data => {
      const box = document.getElementById('resultBox');
      box.innerHTML = '';

      if (data.length > 0) {
        data.forEach(item => {
          const btn = document.createElement('button');
          btn.innerText = `${item.name} - ${item.model} - IMEI: ${item.imei1}`;
          btn.type = 'button';
          btn.style.display = 'block';
          btn.style.marginBottom = '10px';
          btn.onclick = () => {
            document.getElementById('insurance_entry_id').value = item.insurance_entry_id;
            document.getElementById('claimForm').style.display = 'block';
            document.getElementById('infoBox').innerHTML = `
              <b>Selected:</b> ${item.name} (${item.imei1})<br>
              <b>Insurance Value:</b> ₹${item.product_value}<br>
              <b>Premium:</b> ₹${item.premium_amount}<br>
              <b>Total Claims:</b> ${item.total_claims} |
              <b>Total Claimed Value:</b> ₹${item.total_claimed}
            `;
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
