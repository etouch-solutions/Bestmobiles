<?php
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Claim Entry</title>
  <style>
    body {
      font-family: Arial;
      background: #f4f4f4;
      padding: 40px;
    }
    .box {
      max-width: 800px;
      margin: auto;
      background: white;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 0 10px #ccc;
    }
    h2 {
      text-align: center;
    }
    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }
    input, select, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    #suggestions {
      background: #eee;
      margin-top: 5px;
      padding: 10px;
      border-radius: 5px;
    }
    .btn {
      margin-top: 20px;
      background: green;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>Claim Entry</h2>
    <form action="insert_claim_entry.php" method="POST" enctype="multipart/form-data">
      <!-- Customer Search -->
      <label>Search Customer (Name or IMEI)</label>
      <input type="text" id="search" placeholder="Start typing..." autocomplete="off">
      <div id="suggestions"></div>

      <input type="hidden" name="insurance_entry_id" id="insurance_entry_id">

      <!-- Defect Dropdown -->
      <label>Select Defect</label>
      <select name="defect_id" required>
        <option value="">-- Select Defect --</option>
        <?php
        $defects = mysqli_query($conn, "SELECT Defect_Id, Defect_Name FROM Claim_Defects WHERE Is_Active = 1");
        while ($d = mysqli_fetch_assoc($defects)) {
          echo "<option value='{$d['Defect_Id']}'>{$d['Defect_Name']}</option>";
        }
        ?>
      </select>

      <!-- Remarks -->
      <label>Remarks (optional)</label>
      <textarea name="remarks" rows="3"></textarea>

      <!-- Image Upload -->
      <label>Upload Image</label>
      <input type="file" name="claim_image" accept="image/*">

      <!-- Claim Date -->
      <label>Claim Date</label>
      <input type="date" name="claim_date" required>

      <!-- Submit Button -->
      <button type="submit" class="btn">Submit Claim</button>
    </form>
  </div>

  <script>
    document.getElementById('search').addEventListener('input', function () {
      const q = this.value;
      if (q.length < 2) {
        document.getElementById('suggestions').innerHTML = '';
        return;
      }
      fetch(`search_insurance_entry.php?q=${encodeURIComponent(q)}`)
        .then(res => res.json())
        .then(data => {
          let html = '';
          if (data.length === 0) {
            html = '<p>No matches found</p>';
          } else {
            data.forEach(row => {
              html += `<p onclick="selectCustomer(${row.Insurance_Entry_Id}, '${row.Cus_Name}', '${row.Product_Model_Name}')">
                        <strong>${row.Cus_Name}</strong> - ${row.Product_Model_Name}
                      </p>`;
            });
          }
          document.getElementById('suggestions').innerHTML = html;
        });
    });

    function selectCustomer(id, name, model) {
      document.getElementById('search').value = `${name} (${model})`;
      document.getElementById('insurance_entry_id').value = id;
      document.getElementById('suggestions').innerHTML = '';
    }
  </script>
</body>
</html>
