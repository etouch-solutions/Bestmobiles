<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Claim Entry</title>
  <style>
    body { font-family: Arial; background: #f5f5f5; padding: 50px; }
    .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
    input, select, textarea { width: 100%; padding: 10px; margin-top: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px; }
    button { background: green; color: white; padding: 10px 20px; border: none; border-radius: 5px; }
    .result-box { background: #f0f0f0; padding: 10px; margin-bottom: 20px; }
  </style>
</head>
<body>
<div class="container">
  <h2 style="text-align:center;">Claim Entry</h2>

  <label>Search Customer (Name or IMEI)</label>
  <input type="text" id="search" placeholder="Start typing...">
  <div id="resultBox" class="result-box"></div>

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
