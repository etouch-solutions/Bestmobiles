<!DOCTYPE html>
<html>
<head>
  <title>Claim Entry</title>
  <style>
    body { font-family: Arial; background: #f7f7f7; padding: 20px; }
    .container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
    h2 { text-align: center; }
    label { display: block; margin-top: 15px; font-weight: bold; }
    input, select, textarea { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; }
    button { margin-top: 20px; padding: 10px 20px; background: green; color: white; border: none; border-radius: 5px; cursor: pointer; }
    .result-box { margin-top: 20px; background: #f2f2f2; padding: 10px; border-radius: 5px; }
  </style>
</head>
<body>

<div class="container">
  <h2>Claim Entry</h2>

  <form action="insert_claim_entry.php" method="POST" enctype="multipart/form-data">

    <label for="search_customer">Search Customer (Name or IMEI)</label>
    <input type="text" id="search_customer" onkeyup="searchCustomer(this.value)" placeholder="Start typing to search...">

    <div id="search_result" class="result-box"></div>

    <input type="hidden" name="insurance_entry_id" id="insurance_entry_id">

    <label for="defect_id">Select Defect</label>
    <select name="defect_id" required>
      <option value="">-- Select Defect --</option>
      <?php
        include 'db.php';
        $conn = mysqli_connect($host, $user, $pass, $db);
        $defects = mysqli_query($conn, "SELECT Defect_Id, Defect_Name FROM Claim_Defects WHERE Is_Active = 1");
        while ($d = mysqli_fetch_assoc($defects)) {
          echo "<option value='{$d['Defect_Id']}'>{$d['Defect_Name']}</option>";
        }
      ?>
    </select>

    <label for="claim_date">Claim Date</label>
    <input type="date" name="claim_date" required>

    <label for="remarks">Remarks</label>
    <textarea name="remarks" rows="4"></textarea>

    <label for="claim_image">Upload Defect Image</label>
    <input type="file" name="claim_image" accept="image/*">

    <button type="submit">Submit Claim</button>
  </form>
</div>

<script>
function searchCustomer(keyword) {
  if (keyword.length < 3) {
    document.getElementById('search_result').innerHTML = "Type at least 3 characters...";
    return;
  }

  fetch('search_insurance_entry.php?keyword=' + keyword)
    .then(response => response.text())
    .then(data => {
      document.getElementById('search_result').innerHTML = data;
    });
}

function selectInsuranceEntry(id, name) {
  document.getElementById('insurance_entry_id').value = id;
  document.getElementById('search_result').innerHTML = "Selected: " + name;
}
</script>

</body>
</html>
