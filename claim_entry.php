<?php
include 'db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $insurance_id = $_POST['insurance_entry_id'];
    $defect_id = $_POST['defect_id'];
    $claim_date = date('Y-m-d');
    $created_at = date('Y-m-d H:i:s');

    // Upload directory
    $uploadDir = 'uploads/claims/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = time() . '_' . basename($_FILES['claim_image']['name']);
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['claim_image']['tmp_name'], $targetPath)) {
        $stmt = "INSERT INTO Claim_Entry (Insurance_Entry_Id, Defect_Id, Claim_Status, Claim_Date, Claim_Image_Path, Created_At)
                 VALUES ('$insurance_id', '$defect_id', 'Pending', '$claim_date', '$targetPath', '$created_at')";

        if (mysqli_query($conn, $stmt)) {
            echo "Claim submitted successfully.";
        } else {
            echo "Database error: " . mysqli_error($conn);
        }
    } else {
        echo "Failed to upload image.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Claim Entry</title>
  <style>
    body { font-family: Arial; background: #f5f5f5; padding: 20px; }
    .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
    input, select, textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
    button { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
    .result-box button { display: block; width: 100%; margin-bottom: 5px; background: #f1f1f1; padding: 10px; border: none; text-align: left; cursor: pointer; }
    .result-box button:hover { background: #ddd; }
    .claim-item { margin-top: 20px; padding: 15px; background: #fafafa; border-left: 4px solid #28a745; border-radius: 5px; }
    .claim-item img { max-width: 120px; display: block; margin-top: 10px; }
  </style>
</head>
<body>
  <div class="container">
    <h2 style="text-align:center;">Claim Entry</h2>

    <!-- Search -->
    <label>Search Customer (Name, Phone or IMEI)</label>
    <input type="text" id="search" placeholder="Start typing customer name or IMEI...">
    <div id="resultBox" class="result-box"></div>

    <!-- Claim Form -->
    <form id="claimForm" action="insert_claim_entry.php" method="POST" enctype="multipart/form-data" style="display:none;">
      <input type="hidden" name="insurance_entry_id" id="insurance_entry_id">
<label>Select Defect</label>
<select name="defect_id" required>
 
  <?php
    $res = mysqli_query($conn, "SELECT Defect_Id, Defect_Name FROM Defect_Master WHERE Is_Active = 1");
    if ($res && mysqli_num_rows($res) > 0) {
      while ($row = mysqli_fetch_assoc($res)) {
        echo "<option value='{$row['Defect_Id']}'>{$row['Defect_Name']}</option>";
      }
    } else {
      echo "<option disabled>No defects found</option>";
    }
  ?>
</select>

      <label>Remarks (Optional)</label>
      <textarea name="remarks" rows="3"></textarea>

      <label>Upload Product Image</label>
      <input type="file" name="claim_image" accept="image/*" required>

      <button type="submit">Submit Claim</button>
    </form>

    <!-- Show Previous Claims -->
    <div class="claim-list">
      <h3>Previous Claims</h3>
      <?php
       $query = "
  SELECT ce.*, cm.Cus_Name, ie.Product_Model_Name, cd.Defect_Name
  FROM Claim_Entry ce
  JOIN Insurance_Entry ie ON ce.Insurance_Entry_Id = ie.Insurance_Entry_Id
  JOIN Customer_Master cm ON ie.Cus_Id = cm.Cus_Id
  LEFT JOIN Claim_Defects cd ON ce.Defect_Id = cd.Defect_Id
  ORDER BY ce.Claim_Id DESC
";

        $claims = mysqli_query($conn, $query);
        if (mysqli_num_rows($claims) > 0) {
          while ($c = mysqli_fetch_assoc($claims)) {
            echo "<div class='claim-item'>
              <strong>{$c['Cus_Name']}</strong> - {$c['Product_Model_Name']}<br>
              <strong>Defect:</strong> {$c['Defect_Name']}<br>
              <strong>Remarks:</strong> " . (!empty($c['Remarks']) ? $c['Remarks'] : "N/A") . "<br>
              <strong>Date:</strong> {$c['Created_At']}<br>";
              if (!empty($c['Claim_Image_Path'])) {
                echo "<img src='{$c['Claim_Image_Path']}' alt='Claim Image'>";
              }
            echo "</div>";
          }
        } else {
          echo "<p>No claims found.</p>";
        }
      ?>
    </div>
  </div>

  <script>
  document.getElementById('search').addEventListener('input', function () {
    const query = this.value.trim();
    const resultBox = document.getElementById('resultBox');
    if (query.length < 2) {
      resultBox.innerHTML = '';
      return;
    }

    fetch(`search_insurance_entry.php?q=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        resultBox.innerHTML = '';
        if (data.length > 0) {
          data.forEach(item => {
            const btn = document.createElement('button');
            btn.innerText = `${item.name} - ${item.model} - IMEI: ${item.imei1}`;
            btn.onclick = () => {
              document.getElementById('insurance_entry_id').value = item.insurance_entry_id;
              document.getElementById('claimForm').style.display = 'block';
              resultBox.innerHTML = `<strong>Selected:</strong> ${item.name} - ${item.model}`;
            };
            resultBox.appendChild(btn);
          });
        } else {
          resultBox.innerHTML = "<p>No matching customers found.</p>";
        }
      })
      .catch(err => {
        resultBox.innerHTML = "<p>Error fetching data.</p>";
        console.error(err);
      });
  });
</script>

</body>
</html>
