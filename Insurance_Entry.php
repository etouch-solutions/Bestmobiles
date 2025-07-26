<?php
include 'db.php';
?>

<!-- insurance_entry.php -->
<!DOCTYPE html>
<html>
<head>
  <title>Insurance Entry</title>
  <style>
    body { font-family: Arial; background: #f0f0f0; padding: 20px; }
    .container { display: flex; gap: 20px; }
    form, .preview { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #ccc; width: 50%; }
    label { font-weight: bold; display: block; margin-top: 10px; }
    input, select { width: 100%; padding: 10px; margin: 5px 0 15px; }
    .preview h3 { margin-bottom: 10px; }
    button { padding: 10px 20px; background: green; color: white; border: none; border-radius: 5px; }
  </style>
  <script>
    function updatePreview() {
      const fields = ['cus_id', 'brand_id', 'insurance_id', 'staff_id', 'product_model_name', 'imei_1', 'imei_2', 'product_value', 'insurance_start', 'insurance_end', 'premium_amount'];
      fields.forEach(field => {
        const val = document.getElementById(field)?.value || '';
        document.getElementById('preview_' + field).innerText = val;
      });
    }
  </script>
</head>
<body>
  <h2>Insurance Entry</h2>
  <div class="container">
    <form action="insert_insurance_entry.php" method="POST" enctype="multipart/form-data" oninput="updatePreview()">
      <?php include 'db.php'; ?>

      <label>Customer</label>
      <select name="cus_id" id="cus_id">
        <?php
        $q = mysqli_query($conn, "SELECT * FROM Customer_Master WHERE Is_Active = 1");
        while ($r = mysqli_fetch_assoc($q)) {
          echo "<option value='{$r['Cus_Id']}'>{$r['Cus_Name']} ({$r['Cus_CNo']})</option>";
        }
        ?>
      </select>

      <label>Brand</label>
      <select name="brand_id" id="brand_id">
        <?php
        $q = mysqli_query($conn, "SELECT * FROM Brands_Master WHERE Is_Active = 1");
        while ($r = mysqli_fetch_assoc($q)) {
          echo "<option value='{$r['Brand_Id']}'>{$r['Brand_Name']}</option>";
        }
        ?>
      </select>

      <label>Insurance Plan</label>
      <select name="insurance_id" id="insurance_id" onchange="autoPremium()">
        <?php
        $q = mysqli_query($conn, "SELECT * FROM Insurance_Master WHERE Insurance_Status = 1");
        while ($r = mysqli_fetch_assoc($q)) {
          echo "<option value='{$r['Insurance_Id']}' data-premium='{$r['Premium_Amount']}'>{$r['Insurance_Name']}</option>";
        }
        ?>
      </select>

      <label>Staff</label>
      <select name="staff_id" id="staff_id">
        <?php
        $q = mysqli_query($conn, "SELECT * FROM Staff_Master WHERE Staff_Status = 1");
        while ($r = mysqli_fetch_assoc($q)) {
          echo "<option value='{$r['Staff_Id']}'>{$r['Staff_Name']}</option>";
        }
        ?>
      </select>

      <label>Product Model Name</label>
      <input type="text" name="product_model_name" id="product_model_name">

      <label>IMEI 1</label>
      <input type="text" name="imei_1" id="imei_1">

      <label>IMEI 2</label>
      <input type="text" name="imei_2" id="imei_2">

      <label>Product Value (₹)</label>
      <input type="number" name="product_value" id="product_value">

      <label>Insurance Start Date</label>
      <input type="date" name="insurance_start" id="insurance_start">

      <label>Insurance End Date</label>
      <input type="date" name="insurance_end" id="insurance_end">

      <label>Premium Amount</label>
      <input type="number" name="premium_amount" id="premium_amount">

      <label>Upload Bill Photo</label>
      <input type="file" name="bill_photo">

      <label>Upload Product Photo</label>
      <input type="file" name="product_photo">

      <button type="submit">Submit</button>
    </form>

    <div class="preview">
      <h3>Preview</h3>
      <p><strong>Customer ID:</strong> <span id="preview_cus_id"></span></p>
      <p><strong>Brand:</strong> <span id="preview_brand_id"></span></p>
      <p><strong>Insurance Plan:</strong> <span id="preview_insurance_id"></span></p>
      <p><strong>Staff:</strong> <span id="preview_staff_id"></span></p>
      <p><strong>Model:</strong> <span id="preview_product_model_name"></span></p>
      <p><strong>IMEI 1:</strong> <span id="preview_imei_1"></span></p>
      <p><strong>IMEI 2:</strong> <span id="preview_imei_2"></span></p>
      <p><strong>Value:</strong> ₹<span id="preview_product_value"></span></p>
      <p><strong>Start:</strong> <span id="preview_insurance_start"></span></p>
      <p><strong>End:</strong> <span id="preview_insurance_end"></span></p>
      <p><strong>Premium:</strong> ₹<span id="preview_premium_amount"></span></p>
      <button onclick="window.print()">Print</button>
    </div>
  </div>

  <script>
    function autoPremium() {
      let select = document.getElementById('insurance_id');
      let premium = select.options[select.selectedIndex].getAttribute('data-premium');
      document.getElementById('premium_amount').value = premium;
      updatePreview();
    }
    window.onload = updatePreview;
  </script>
</body>
</html>
