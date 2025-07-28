<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Claim Entry</title>
  <style>
    body { font-family: Arial; background: #f5f5f5; padding: 30px; }
    .container { max-width: 600px; background: white; padding: 20px; margin: auto; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
    input, select { width: 100%; padding: 8px; margin-bottom: 15px; }
    button { padding: 10px 20px; background: green; color: white; border: none; border-radius: 5px; }
  </style>
</head>
<body>
  <div class="container">
    <h2 style="text-align:center;">Claim Entry</h2>

    <label>Search Customer (Name or IMEI)</label>
    <input type="text" id="searchBox" oninput="searchCustomer(this.value)" placeholder="Start typing to search...">
    <div id="searchResults"></div>

    <form id="claimForm" action="insert_claim_entry.php" method="POST" enctype="multipart/form-data" style="display:none;">
      <input type="hidden" name="insurance_entry_id" id="insurance_entry_id">

      <label>Select Defect</label>
      <select name="defect_id" required>
        <option value="">-- Select Defect --</option>
        <?php
          $res = mysqli_query($conn, "SELECT Defect_Id, Defect_Name FROM Claim_Defects WHERE Status = 1");
          while ($row = mysqli_fetch_assoc($res)) {
            echo "<option value='{$row['Defect_Id']}'>{$row['Defect_Name']}</option>";
          }
        ?>
      </select>

      <label>Remarks</label>
      <textarea name="remarks"></textarea>

      <label>Upload Image (Optional)</label>
      <input type="file" name="claim_image" accept="image/*">

      <button type="submit">Submit Claim</button>
    </form>
  </div>

<script>
function searchCustomer(query) {
  if (query.length < 2) {
    document.getElementById("searchResults").innerHTML = "";
    return;
  }

  fetch("search_insurance_entry.php?q=" + encodeURIComponent(query))
    .then(res => res.json())
    .then(data => {
      const resDiv = document.getElementById("searchResults");
      resDiv.innerHTML = "";

      if (data.length === 0) {
        resDiv.innerHTML = "<p>No matching customers found.</p>";
        return;
      }

      data.forEach(entry => {
        const div = document.createElement("div");
        div.textContent = `${entry.Cus_Name} (${entry.Product_Model_Name} - IMEI: ${entry.IMEI_1})`;
        div.style.cursor = "pointer";
        div.style.padding = "5px";
        div.style.borderBottom = "1px solid #ccc";
        div.onclick = function() {
          document.getElementById("insurance_entry_id").value = entry.Insurance_Entry_Id;
          document.getElementById("claimForm").style.display = "block";
          resDiv.innerHTML = `<p>Selected: ${entry.Cus_Name} (${entry.Product_Model_Name})</p>`;
        };
        resDiv.appendChild(div);
      });
    });
}
</script>
</body>
</html>
