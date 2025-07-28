<!DOCTYPE html>
<html>
<head>
  <title>Add Insurance Entry</title>
  <style>
    body { font-family: Arial; background: #f2f2f2; padding: 20px; }
    .container { display: flex; gap: 20px; max-width: 1200px; margin: auto; }
    form, .preview { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; width: 50%; }
    label { font-weight: bold; margin-top: 10px; display: block; }
    input, select, textarea { width: 100%; padding: 8px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; }
    button { padding: 10px 20px; background: green; color: white; border: none; border-radius: 5px; cursor: pointer; }
    img { width: 100px; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px; }
    .preview-item { margin-bottom: 10px; }
  </style>
</head>
<body>

<h2 style="text-align:center">Add Insurance Entry</h2>

<div class="container">
  <form action="insert_insurance_entry.php" method="POST" enctype="multipart/form-data">
    <?php include 'db.php'; $conn = mysqli_connect($host, $user, $pass, $db); ?>

    <!-- Customer Dropdown -->
    <label>Select Customer</label>
    <select name="cus_id" id="cus_id" onchange="loadCustomerDetails(this)">
      <option value="">-- Select --</option>
      <?php
        $res = mysqli_query($conn, "SELECT Cus_Id, Cus_Name, Cus_CNo FROM Customer_Master WHERE Is_Active = 1");
        while ($row = mysqli_fetch_assoc($res)) {
          echo "<option value='{$row['Cus_Id']}' data-name='{$row['Cus_Name']}' data-contact='{$row['Cus_CNo']}'>{$row['Cus_Name']}</option>";
        }
      ?>
    </select>

    <!-- Brand -->
    <label>Select Brand</label>
    <select name="brand_id" id="brand_id" onchange="updatePreview('brand', this)">
      <option value="">-- Select --</option>
      <?php
        $res = mysqli_query($conn, "SELECT Brand_Id, Brand_Name FROM Brands_Master WHERE Is_Active = 1");
        while ($row = mysqli_fetch_assoc($res)) {
          echo "<option value='{$row['Brand_Id']}'>{$row['Brand_Name']}</option>";
        }
      ?>
    </select>

    <!-- Insurance Plan -->
    <label>Select Insurance Plan</label>
    <select name="insurance_id" id="insurance_id" onchange="calculatePremium(); updateInsuranceDetails(this);">
      <option value="">-- Select --</option>
      <?php
        $res = mysqli_query($conn, "SELECT Insurance_Id, Insurance_Name, Premium_Percentage, Duration_Months FROM Insurance_Master WHERE Insurance_Status = 1");
        while ($row = mysqli_fetch_assoc($res)) {
          echo "<option value='{$row['Insurance_Id']}' data-premium='{$row['Premium_Percentage']}' data-duration='{$row['Duration_Months']}'>{$row['Insurance_Name']}</option>";
        }
      ?>
    </select>

    <!-- Staff -->
    <label>Select Staff</label>
    <select name="staff_id" id="staff_id" onchange="updatePreview('staff', this)">
      <option value="">-- Select --</option>
      <?php
        $res = mysqli_query($conn, "SELECT Staff_Id, Staff_Name FROM Staff_Master WHERE Staff_Status = 1");
        while ($row = mysqli_fetch_assoc($res)) {
          echo "<option value='{$row['Staff_Id']}'>{$row['Staff_Name']}</option>";
        }
      ?>
    </select>

    <!-- Product Details -->
    <label>Product Model Name</label>
    <input type="text" name="product_model_name" oninput="updatePreview('model', this)" required>

    <label>IMEI 1</label>
    <input type="text" name="imei_1" oninput="updatePreview('imei1', this)" required>

    <label>IMEI 2</label>
    <input type="text" name="imei_2" oninput="updatePreview('imei2', this)">

    <label>Product Value (₹)</label>
    <input type="number" name="product_value" id="product_value" oninput="calculatePremium()" required>

    <label>Premium Amount (₹)</label>
    <input type="number" name="premium_amount" id="premium_amount" readonly>

    <!-- Uploads -->
    <label>Product Photo</label>
    <input type="file" name="product_photo" accept="image/*" onchange="previewImage(this, 'productPreview')">
    <img id="productPreview">

    <label>Bill Photo</label>
    <input type="file" name="bill_photo" accept="image/*" onchange="previewImage(this, 'billPreview')">
    <img id="billPreview">

    <!-- Dates -->
    <label>Bill Date</label>
    <input type="date" name="bill_date" onchange="updatePreview('bill_date', this)">

    <label>Insurance Start</label>
    <input type="date" name="insurance_start" id="insurance_start" onchange="autoSetEndDate()">

    <label>Insurance End</label>
    <input type="date" name="insurance_end" id="insurance_end" readonly>

    <label>Insurance Status</label>
    <select name="insurance_status" onchange="updatePreview('ins_status', this)">
      <option value="1">Valid</option>
      <option value="0">Invalid</option>
    </select>

    <label>Product Insurance Status</label>
    <select name="product_ins_status" onchange="updatePreview('prod_ins_status', this)">
      <option value="1">Active</option>
      <option value="0">Inactive</option>
    </select>

    <button type="submit">Submit</button>
  </form>

  <!-- Preview -->
  <div class="preview">
    <h3>Preview</h3>
    <div class="preview-item" id="previewCustomer">Customer: -</div>
    <div class="preview-item" id="previewBrand">Brand: -</div>
    <div class="preview-item" id="previewInsurance">Insurance Plan: -</div>
    <div class="preview-item" id="previewStaff">Staff: -</div>
    <div class="preview-item" id="previewModel">Model: -</div>
    <div class="preview-item" id="previewIMEI1">IMEI 1: -</div>
    <div class="preview-item" id="previewIMEI2">IMEI 2: -</div>
    <div class="preview-item" id="previewValue">Value: ₹-</div>
    <div class="preview-item" id="previewPremium">Premium: ₹-</div>
    <div class="preview-item" id="previewBillDate">Bill Date: -</div>
    <div class="preview-item" id="previewStart">Start Date: -</div>
    <div class="preview-item" id="previewEnd">End Date: -</div>
    <div class="preview-item" id="previewInsStatus">Insurance Status: -</div>
    <div class="preview-item" id="previewProdInsStatus">Product Insurance Status: -</div>
  </div>
</div>

<script>
function updatePreview(field, el) {
  const value = el.value || '-';
  const map = {
    brand: 'previewBrand',
    staff: 'previewStaff',
    model: 'previewModel',
    imei1: 'previewIMEI1',
    imei2: 'previewIMEI2',
    bill_date: 'previewBillDate',
    ins_status: 'previewInsStatus',
    prod_ins_status: 'previewProdInsStatus'
  };
  document.getElementById(map[field]).innerText = el.options ? el.options[el.selectedIndex].text : value;
}

function loadCustomerDetails(el) {
  const name = el.options[el.selectedIndex].getAttribute('data-name');
  const contact = el.options[el.selectedIndex].getAttribute('data-contact');
  document.getElementById('previewCustomer').innerText = `Customer: ${name}, Contact: ${contact}`;
}

function updateInsuranceDetails(el) {
  const name = el.options[el.selectedIndex].text;
  const premium = el.options[el.selectedIndex].getAttribute('data-premium');
  const duration = el.options[el.selectedIndex].getAttribute('data-duration');
  document.getElementById('previewInsurance').innerText = `${name} (${premium}% / ${duration} months)`;
  autoSetEndDate();
  calculatePremium();
}

function calculatePremium() {
  const productValue = parseFloat(document.getElementById('product_value').value) || 0;
  const insurance = document.getElementById('insurance_id');
  const premiumPercent = parseFloat(insurance.options[insurance.selectedIndex]?.getAttribute('data-premium')) || 0;
  const premium = (productValue * premiumPercent) / 100;
  document.getElementById('premium_amount').value = premium.toFixed(2);
  document.getElementById('previewValue').innerText = `Value: ₹${productValue}`;
  document.getElementById('previewPremium').innerText = `Premium: ₹${premium.toFixed(2)}`;
}

function autoSetEndDate() {
  const start = document.getElementById('insurance_start').value;
  const duration = parseInt(document.getElementById('insurance_id')?.selectedOptions[0]?.getAttribute('data-duration')) || 0;
  if (start && duration > 0) {
    const startDate = new Date(start);
    startDate.setMonth(startDate.getMonth() + duration);
    const endDateStr = startDate.toISOString().split('T')[0];
    document.getElementById('insurance_end').value = endDateStr;
    document.getElementById('previewStart').innerText = `Start Date: ${start}`;
    document.getElementById('previewEnd').innerText = `End Date: ${endDateStr}`;
  }
}

function previewImage(input, targetId) {
  const reader = new FileReader();
  reader.onload = function (e) {
    document.getElementById(targetId).src = e.target.result;
  };
  reader.readAsDataURL(input.files[0]);
}
</script>
</body>
</html>
