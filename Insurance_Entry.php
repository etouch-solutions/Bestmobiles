<!DOCTYPE html>
<html>
<head>
  <title>Add Insurance Entry</title>
  <style>
    body { font-family: Arial; background: #f2f2f2; padding: 20px; }
    .container { display: flex; justify-content: space-between; max-width: 1100px; margin: auto; }
    form, .preview { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; width: 48%; }
    label { font-weight: bold; margin-top: 10px; display: block; }
    input, select { width: 100%; padding: 8px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; }
    button { padding: 10px 20px; background: green; color: white; border: none; border-radius: 5px; cursor: pointer; }
    .preview h3 { border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 20px; }
    .preview-item { margin-bottom: 10px; }
  </style>
</head>
<body>
<h2 style="text-align:center">Add Insurance Entry</h2>
<div class="container">
  <form action="insert_insurance_entry.php" method="POST" enctype="multipart/form-data">
    <?php include 'db.php'; $conn = mysqli_connect($host, $user, $pass, $db); ?>

    <label>Select Customer</label>
    <select name="cus_id" id="cus_id" onchange="updatePreview()">
      <option value="">-- Select --</option>
      <?php
        $res = mysqli_query($conn, "SELECT Cus_Id, Cus_Name, Cus_CNo FROM Customer_Master WHERE Is_Active = 1");
        while ($row = mysqli_fetch_assoc($res)) {
          echo "<option value='{$row['Cus_Id']}' data-name='{$row['Cus_Name']}' data-phone='{$row['Cus_CNo']}'>{$row['Cus_Name']}</option>";
        }
      ?>
    </select>

    <label>Select Brand</label>
    <select name="brand_id" id="brand_id" onchange="updatePreview()">
      <option value="">-- Select --</option>
      <?php
        $res = mysqli_query($conn, "SELECT Brand_Id, Brand_Name FROM Brands_Master WHERE Is_Active = 1");
        while ($row = mysqli_fetch_assoc($res)) {
          echo "<option value='{$row['Brand_Id']}' data-name='{$row['Brand_Name']}'>{$row['Brand_Name']}</option>";
        }
      ?>
    </select>

    <label>Select Insurance Plan</label>
    <select name="insurance_id" id="insurance_id" onchange="updatePreview(); calculatePremiumAndEndDate();">
      <option value="">-- Select --</option>
      <?php
        $res = mysqli_query($conn, "SELECT Insurance_Id, Insurance_Name, Premium_Percentage, Duration_Months FROM Insurance_Master WHERE Insurance_Status = 1");
        while ($row = mysqli_fetch_assoc($res)) {
          echo "<option value='{$row['Insurance_Id']}' data-name='{$row['Insurance_Name']}' data-premium='{$row['Premium_Percentage']}' data-duration='{$row['Duration_Months']}'>{$row['Insurance_Name']}</option>";
        }
      ?>
    </select>

    <label>Select Staff</label>
    <select name="staff_id" id="staff_id" onchange="updatePreview()">
      <option value="">-- Select --</option>
      <?php
        $res = mysqli_query($conn, "SELECT Staff_Id, Staff_Name FROM Staff_Master WHERE Staff_Status = 1");
        while ($row = mysqli_fetch_assoc($res)) {
          echo "<option value='{$row['Staff_Id']}'>{$row['Staff_Name']}</option>";
        }
      ?>
    </select>

    <label>Product Model Name</label>
    <input type="text" name="product_model_name" id="product_model_name" oninput="updatePreview()" required>

    <label>IMEI 1</label>
    <input type="text" name="imei_1" id="imei_1" oninput="updatePreview()">

    <label>IMEI 2</label>
    <input type="text" name="imei_2" id="imei_2" oninput="updatePreview()">

    <label>Product Value (₹)</label>
    <input type="number" name="product_value" id="product_value" oninput="calculatePremiumAndEndDate(); updatePreview()">

    <label>Calculated Premium (₹)</label>
    <input type="number" name="premium_amount" id="premium_amount" readonly>

    <label>Upload Product Photo</label>
    <input type="file" name="product_photo" accept="image/*">

    <label>Upload Bill Photo</label>
    <input type="file" name="bill_photo" accept="image/*">

    <label>Bill Date</label>
    <input type="date" name="bill_date" id="bill_date" onchange="updatePreview()">

    <label>Insurance Start</label>
    <input type="date" name="insurance_start" id="insurance_start" onchange="calculatePremiumAndEndDate(); updatePreview()">

    <label>Insurance End</label>
    <input type="date" name="insurance_end" id="insurance_end" readonly>

    <label>Insurance Status</label>
    <select name="insurance_status" id="insurance_status" onchange="updatePreview()">
      <option value="1">Valid</option>
      <option value="0">Invalid</option>
    </select>

    <label>Product Insurance Status</label>
    <select name="product_ins_status" id="product_ins_status" onchange="updatePreview()">
      <option value="1">Active</option>
      <option value="0">Inactive</option>
    </select>

    <button type="submit">Submit</button>
  </form>

  <div class="preview">
    <h3>Live Preview</h3>
    <div id="previewContent"></div>
  </div>
</div>

<script>
function updatePreview() {
  const customer = document.getElementById('cus_id');
  const brand = document.getElementById('brand_id');
  const insurance = document.getElementById('insurance_id');
  const model = document.getElementById('product_model_name').value;
  const imei1 = document.getElementById('imei_1').value;
  const imei2 = document.getElementById('imei_2').value;
  const value = document.getElementById('product_value').value;
  const premium = document.getElementById('premium_amount').value;
  const billDate = document.getElementById('bill_date').value;
  const startDate = document.getElementById('insurance_start').value;
  const endDate = document.getElementById('insurance_end').value;
  const insStatus = document.getElementById('insurance_status').value;
  const prodStatus = document.getElementById('product_ins_status').value;

  const preview = `
    <div class='preview-item'><b>Customer:</b> ${customer.options[customer.selectedIndex]?.dataset.name || ''} (${customer.options[customer.selectedIndex]?.dataset.phone || ''})</div>
    <div class='preview-item'><b>Brand:</b> ${brand.options[brand.selectedIndex]?.dataset.name || ''}</div>
    <div class='preview-item'><b>Insurance:</b> ${insurance.options[insurance.selectedIndex]?.dataset.name || ''}</div>
    <div class='preview-item'><b>Model:</b> ${model}</div>
    <div class='preview-item'><b>IMEI 1:</b> ${imei1}</div>
    <div class='preview-item'><b>IMEI 2:</b> ${imei2}</div>
    <div class='preview-item'><b>Product Value:</b> ₹${value}</div>
    <div class='preview-item'><b>Premium:</b> ₹${premium}</div>
    <div class='preview-item'><b>Bill Date:</b> ${billDate}</div>
    <div class='preview-item'><b>Insurance Start:</b> ${startDate}</div>
    <div class='preview-item'><b>Insurance End:</b> ${endDate}</div>
    <div class='preview-item'><b>Insurance Status:</b> ${insStatus == 1 ? 'Valid' : 'Invalid'}</div>
    <div class='preview-item'><b>Product Status:</b> ${prodStatus == 1 ? 'Active' : 'Inactive'}</div>
  `;

  document.getElementById('previewContent').innerHTML = preview;
}

function calculatePremiumAndEndDate() {
  const insurance = document.getElementById('insurance_id');
  const selectedOption = insurance.options[insurance.selectedIndex];
  const percentage = selectedOption.getAttribute('data-premium');
  const duration = selectedOption.getAttribute('data-duration');
  const productValue = parseFloat(document.getElementById('product_value').value) || 0;

  if (percentage && productValue) {
    const premium = (productValue * parseFloat(percentage)) / 100;
    document.getElementById('premium_amount').value = premium.toFixed(2);
  }

  const startDate = document.getElementById('insurance_start').value;
  if (startDate && duration) {
    const start = new Date(startDate);
    start.setMonth(start.getMonth() + parseInt(duration));
    document.getElementById('insurance_end').value = start.toISOString().split('T')[0];
  }
}
</script>
</body>
</html>
