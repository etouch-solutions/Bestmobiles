<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Claim Entry</title>
  <style>
    body { font-family: Arial; background: #f5f5f5; padding: 50px; }
    .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
    input, select, textarea { width: 100%; padding: 10px; margin-top: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px; }
    button { background: green; color: white; padding: 10px 20px; border: none; border-radius: 5px; }
    .result-box { background: #f0f0f0; padding: 10px; margin-bottom: 20px; }
    .claim-list { background: #f9f9f9; padding: 15px; margin-top: 40px; border: 1px solid #ddd; border-radius: 10px; }
    .claim-item { margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #ccc; }
  </style>
</head>
<body>
<div class="container">
  <h2 style="text-align:center;">Claim Entry</h2>

  <!-- Search -->
  <label>Search Customer (Name or IMEI)</label>
  <input type="text" id="search" placeholder="Start typing...">
  <div id="resultBox" class="result-box"></div>

  <!-- Form -->
  <form action="insert_claim_entry.php" method="POST" enctype="multipart/form-data" style="display:none;" id="claimForm">
    <input type="hidden" name="insurance_entry_id" id="insurance_entry_id">

    <label>Select Defect</label>
    <select name="defect_id" required>
      <option value="">-- Select Defect --</option>
      <?php
        $res = mysqli_query($conn, "SELECT Defect_Id, Defect_Description FROM Claim_Defects");
        while ($row = mysqli_fetch_assoc($res)) {
          echo "<option value='{$row['Defect_Id']}'>{$row['Defect_Description']}</option>";
        }
      ?>
    </select>

    <label>Remarks (optional)</label>
    <textarea name="remarks"></textarea>

    <label>Upload Product Image</label>
    <input type="file" name="claim_image" accept="image/*" required>

    <button type="submit">Submit Claim</button>
  </form>

  <!-- Claim List -->
  <div class="claim-list">
    <h3>All Claims</h3>
    <?php
      $query = "
        SELECT c.*, i.IMEI_1, cm.Cus_Name, cm.Cus_CNo, cd.Defect_Description 
        FROM Claim_Entry c
        LEFT JOIN Insurance_Entry i ON c.Insurance_Entry_Id = i.Insurance_Entry_Id
        LEFT JOIN Customer_Master cm ON i.Cus_Id = cm.Cus_Id
        LEFT JOIN Claim_Defects cd ON c.Defect_Id = cd.Defect_Id
        ORDER BY c.Claim_Id DESC
      ";
      $res = mysqli_query($conn, $query);
      while ($c = mysqli_fetch_assoc($res)) {
        echo "<div class='claim-item'>
          <b>Claim ID:</b> {$c['Claim_Id']}<br>
          <b>Customer:</b> {$c['Cus_Name']} ({$c['Cus_CNo']})<br>
          <b>IMEI:</b> {$c['IMEI_1']}<br>
          <b>Defect:</b> {$c['Defect_Description']}<br>
          <b>Remarks:</b> {$c['Remarks']}<br>
          <b>Date:</b> {$c['Created_At']}<br>";
        if (!empty($c['Claim_Image_Path'])) {
          echo "<b>Image:</b><br><img src='{$c['Claim_Image_Path']}' alt='Claim Image' width='150'>";
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
