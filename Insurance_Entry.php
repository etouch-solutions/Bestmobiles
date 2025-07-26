<!DOCTYPE html>
<html>
<head>
  <title>Insurance Entry Form</title>
  <style>
    body {
      font-family: Arial;
      background: #f2f2f2;
      padding: 20px;
    }
    .container {
      display: flex;
      justify-content: space-between;
      max-width: 1200px;
      margin: auto;
    }
    form, .preview {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px #ccc;
      width: 48%;
    }
    label {
      font-weight: bold;
      margin-top: 10px;
      display: block;
    }
    input, select {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    button {
      padding: 12px 20px;
      background: #28a745;
      color: white;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
    }
    button:hover {
      background: #218838;
    }
    img.preview-img {
      width: 100px;
      margin-top: 10px;
    }
    h2, h3 {
      text-align: center;
    }
    .section {
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
<h2>Insurance Entry Form</h2>
<div class="container">
  <form action="insert_insurance_entry.php" method="POST" enctype="multipart/form-data">
    <?php include 'db.php'; $conn = mysqli_connect($host, $user, $pass, $db); ?>

    <div class="section">
      <label>Select Customer</label>
      <select name="cus_id" id="cus_id" onchange="loadCustomerDetails(this.value)" required>
        <option value="">-- Select --</option>
        <?php
          $res = mysqli_query($conn, "SELECT Cus_Id, Cus_Name FROM Customer_Master WHERE Is_Active = 1");
          while ($row = mysqli_fetch_assoc($res)) {
            echo "<option value='{$row['Cus_Id']}'>{$row['Cus_Name']}</option>";
          }
        ?>
      </select>

      <label>Select Brand</label>
      <select name="brand_id" required>
        <option value="">-- Select --</option>
        <?php
          $res = mysqli_query($conn, "SELECT Brand_Id, Brand_Name FROM Brands_Master WHERE Is_Active = 1");
          while ($row = mysqli_fetch_assoc($res)) {
            echo "<option value='{$row['Brand_Id']}'>{$row['Brand_Name']}</option>";
          }
        ?>
      </select>

      <label>Select Insurance Plan</label>
      <select name="insurance_id" id="insurance_id" onchange="calculatePremiumAndEndDate()" required>
        <option value="">-- Select --</option>
        <?php
          $res = mysqli_query($conn, "SELECT Insurance_Id, Insurance_Name, Premium_Percentage, Insurance_Duration_Months FROM Insurance_Master WHERE Insurance_Status = 1");
          while ($row = mysqli_fetch_assoc($res)) {
            echo "<option value='{$row['Insurance_Id']}' data-premium='{$row['Premium_Percentage']}' data-duration='{$row['Insurance_Duration_Months']}'>{$row['Insurance_Name']}</option>";
          }
        ?>
      </select>

      <label>Select Staff</label>
      <select name="staff_id" required>
        <option value="">-- Select --</option>
        <?php
          $res = mysqli_query($conn, "SELECT Staff_Id, Staff_Name FROM Staff_Master WHERE Staff_Status = 1");
          while ($row = mysqli_fetch_assoc($res)) {
            echo "<option value='{$row['Staff_Id']}'>{$row['Staff_Name']}</option>";
          }
        ?>
      </select>
    </div>

    <div class="section">
      <label>Product Model Name</label>
      <input type="text" name="product_model_name" required>

      <label>IMEI 1</label>
      <input type="text" name="imei_1">

      <label>IMEI 2</label>
      <input type="text" name="imei_2">

      <label>Product Value (₹)</label>
      <input type="number" name="product_value" id="product_value" oninput="calculatePremiumAndEndDate()">

      <label>Calculated Premium (₹)</label>
      <input type="number" name="premium_amount" id="premium_amount" readonly>
    </div>

    <div class="section">
      <label>Upload Product Photo</label>
      <input type="file" name="product_photo" accept="image/*">

      <label>Upload Bill Photo</label>
      <input type="file" name="bill_photo" accept="image/*">
    </div>

    <div class="section">
      <label>Bill Date</label>
      <input type="date" name="bill_date">

      <label>Insurance Start</label>
      <input type="date" name="insurance_start" id="insurance_start" onchange="calculatePremiumAndEndDate()">

      <label>Insurance End</label>
      <input type="date" name="insurance_end" id="insurance_end" readonly>
    </div>

    <div class="section">
      <label>Insurance Status</label>
      <select name="insurance_status">
        <option value="1">Valid</option>
        <option value="0">Invalid</option>
      </select>

      <label>Product Insurance Status</label>
      <select name="product_ins_status">
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>
    </div>

    <button type="submit">Submit</button>
  </form>

  <div class="preview">
    <h3>Customer & Insurance Preview</h3>
    <div id="customerDetails">Select a customer to view full details.</div>
  </div>
</div>

<script>
function loadCustomerDetails(cus_id) {
  if (!cus_id) {
    document.getElementById('customerDetails').innerHTML = "Select a customer to view full details.";
    return;
  }
  fetch(`fetch_customer.php?cus_id=${cus_id}`)
    .then(res => res.text())
    .then(data => document.getElementById('customerDetails').innerHTML = data);
}

function calculatePremiumAndEndDate() {
  const insuranceSelect = document.getElementById('insurance_id');
  const selectedOption = insuranceSelect.options[insuranceSelect.selectedIndex];
  const percentage = parseFloat(selectedOption.getAttribute('data-premium')) || 0;
  const duration = parseInt(selectedOption.getAttribute('data-duration')) || 0;
  const productValue = parseFloat(document.getElementById('product_value').value) || 0;

  if (percentage > 0 && productValue > 0) {
    const premium = (productValue * percentage) / 100;
    document.getElementById('premium_amount').value = premium.toFixed(2);
  }

  const startDate = document.getElementById('insurance_start').value;
  if (startDate && duration > 0) {
    const start = new Date(startDate);
    start.setMonth(start.getMonth() + duration);
    const year = start.getFullYear();
    const month = String(start.getMonth() + 1).padStart(2, '0');
    const day = String(start.getDate()).padStart(2, '0');
    document.getElementById('insurance_end').value = `${year}-${month}-${day}`;
  }
}
</script>
</body>
</html>