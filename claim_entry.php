<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Claim Entry</title>
  <style>
    body { font-family: Arial; background: #f5f5f5; padding: 40px; }
    .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
    input, select, textarea { width: 100%; padding: 10px; margin-top: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px; }
    button { background: green; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
    .result-box { background: #f0f0f0; padding: 10px; margin-bottom: 20px; }
    .claim-history { background: #fafafa; padding: 10px; border: 1px solid #ccc; margin-bottom: 20px; }
  </style>
</head>
<body>
<div class="container">
  <h2 style="text-align:center;">Claim Entry</h2>

  <label>Search Customer (Name / Phone / IMEI)</label>
  <input type="text" id="search" placeholder="Start typing...">
  <div id="resultBox" class="result-box"></div>

  <div id="claimHistory" class="claim-history" style="display:none;"></div>

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
</div>

<script>
document.getElementById('search').addEventListener('input', function () {
  const query = this.value;
  if (query.length < 2) return;

  fetch(`search_insurance_entry.php?q=${query}`)
    .then(res => res.json())
    .then(data => {
      const box = document.getElementById('resultBox');
      const historyBox = document.getElementById('claimHistory');
      box.innerHTML = '';
      historyBox.innerHTML = '';
      document.getElementById('claimForm').style.display = 'none';

      if (data.length > 0) {
        data.forEach(item => {
          const btn = document.createElement('button');
          btn.style.display = "block";
          btn.style.marginBottom = "10px";
          btn.innerText = `${item.name} | ${item.model} | IMEI: ${item.imei1}`;
          btn.onclick = () => {
            document.getElementById('insurance_entry_id').value = item.insurance_entry_id;
            document.getElementById('claimForm').style.display = 'block';

            historyBox.style.display = 'block';
            historyBox.innerHTML = `
              <b>Selected:</b> ${item.name} (${item.model})<br>
              <b>Insurance Value:</b> ₹${item.product_value}<br>
              <b>Premium:</b> ₹${item.premium_amount}<br>
              <b>Total Claims:</b> ${item.total_claims} | <b>Total Claimed Value:</b> ₹${item.total_claimed}<br>
              <hr>
            `;
          };
          box.appendChild(btn);
        });
      } else {
        box.innerText = "No matching customer found.";
      }
    });
});
</script>
</body>
</html>
