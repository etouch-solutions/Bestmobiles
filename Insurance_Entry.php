<!DOCTYPE html>
<html>
<head>
  <title>Add Insurance Entry</title>
  <style>
    body { font-family: Arial; background: #f2f2f2; padding: 20px; }
    .container { display: flex; justify-content: space-between; max-width: 1000px; margin: auto; }
    form, .preview { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; width: 48%; }
    label { font-weight: bold; margin-top: 10px; display: block; }
    input, select { width: 100%; padding: 8px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; }
    button { padding: 10px 20px; background: green; color: white; border: none; border-radius: 5px; cursor: pointer; }
    img { width: 100px; margin-top: 10px; }
  </style>
</head>
<body>
<h2 style="text-align:center">Add Insurance Entry</h2>
<div class="container">
  <form action="insert_insurance_entry.php" method="POST" enctype="multipart/form-data">
    <?php include 'db.php'; $conn = mysqli_connect($host, $user, $pass, $db); ?>

    <!-- Customer Dropdown -->
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

    <!-- Brand -->
    <label>Select Brand</label>
 <select name="brand_id" onchange="loadBrandDetails(this.value)" required>
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
    <select name="insurance_id" id="insurance_id" onchange="calculatePremiumAndEndDate(); loadInsuranceDetails(this.value)" required>

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
    <select name="staff_id" required>
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
  <input type="number" name="product_value" id="product_value" oninput="calculatePremiumAndEndDate(); updatePreview('value', this)" required>

    <label>Calculated Premium (₹)</label>
    <input type="number" name="premium_amount" id="premium_amount" readonly>

    <!-- File Uploads -->
    <label>Upload Product Photo</label>
 <input type="file" name="product_photo" accept="image/*" onchange="previewImage(this, 'previewProductPhoto')">

    <label>Upload Bill Photo</label>
    <input type="file" name="bill_photo" accept="image/*" onchange="previewImage(this, 'previewBillPhoto')">

    <!-- Dates -->
    <label>Bill Date</label>
    <input type="date" name="bill_date" onchange="updatePreview('bill', this)" required>

    <label>Insurance Start</label>
    <input type="date" name="insurance_start" id="insurance_start" onchange="calculatePremiumAndEndDate(); updatePreview('start', this)" required>

    <label>Insurance End</label>
   <input type="date" name="insurance_end" id="insurance_end"  >
    <label>Insurance Status</label>
    <select name="insurance_status" onchange="updatePreview('insStatus', this)">
  <option value="1">Valid</option>
  <option value="0">Invalid</option>
</select>

    <label>Product Insurance Status</label>
    <select name="product_ins_status" onchange="updatePreview('prodStatus', this)">
  <option value="1">Active</option>
  <option value="0">Inactive</option>
</select>

    <button type="submit">Submit</button>
  </form>

  <!-- Live Preview Panel -->
  <div class="preview">
    <h3>Customer Details</h3>
    <div id="customerDetails">Select a customer to view details.</div>
    <div id="brandDetails">Select a brand to view details.</div>
<div id="insuranceDetails">Select a plan to view details.</div>
<h3>Live Preview</h3>
<div id="previewModel">Model: -</div>
<div id="previewIMEI1">IMEI 1: -</div>
<div id="previewIMEI2">IMEI 2: -</div>
<div id="previewValue">Product Value: -</div>
<div id="previewPremium">Premium: -</div>
<div id="previewBillDate">Bill Date: -</div>
<div id="previewStart">Start Date: -</div>
<div id="previewEnd">End Date: -</div>
<div id="previewInsStatus">Insurance Status: -</div>
<div id="previewProdStatus">Product Insurance Status: -</div>
<div>
  Product Photo: <br><img id="previewProductPhoto" width="120" />
</div>
<div>
  Bill Photo: <br><img id="previewBillPhoto" width="120" />
</div>
 
  </div>
</div>

<script>

function updatePreview(type, el) {
  const val = el.value || '-';
  const map = {
    model: 'previewModel',
    imei1: 'previewIMEI1',
    imei2: 'previewIMEI2',
    value: 'previewValue',
    bill: 'previewBillDate',
    start: 'previewStart',
    insStatus: 'previewInsStatus',
    prodStatus: 'previewProdStatus'
  };

  if (type === 'insStatus' || type === 'prodStatus') {
    const text = el.options[el.selectedIndex].text;
    document.getElementById(map[type]).innerText = text;
  } else if (map[type]) {
    document.getElementById(map[type]).innerText = val;
  }
}

function previewImage(input, previewId) {
  const file = input.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = function (e) {
    document.getElementById(previewId).src = e.target.result;
  };
  reader.readAsDataURL(file);
}

// Premium + End Date is already handled by your calculatePremiumAndEndDate()
document.getElementById('insurance_id').addEventListener('change', calculatePremiumAndEndDate);
document.getElementById('product_value').addEventListener('input', calculatePremiumAndEndDate);
document.getElementById('insurance_start').addEventListener('change', calculatePremiumAndEndDate);



function calculatePremiumAndEndDate() {
  const insurance = document.getElementById('insurance_id');
  const selectedOption = insurance.options[insurance.selectedIndex];
  const percentage = selectedOption.getAttribute('data-premium');
  const durationMonths = selectedOption.getAttribute('data-duration');
  const productValue = parseFloat(document.getElementById('product_value').value) || 0;

  // Calculate premium
  if (percentage && productValue) {
    const premium = (productValue * parseFloat(percentage)) / 100;
    document.getElementById('premium_amount').value = premium.toFixed(2);
    document.getElementById('previewPremium').innerText = `₹${premium.toFixed(2)}`;
  }

  // Set End Date
  const startInput = document.getElementById('insurance_start');
  const startDate = new Date(startInput.value);
  if (!isNaN(startDate) && durationMonths) {
    const endDate = new Date(startDate);
    endDate.setMonth(endDate.getMonth() + parseInt(durationMonths));
    const isoEndDate = endDate.toISOString().split('T')[0];
    document.getElementById('insurance_end').value = isoEndDate;
    document.getElementById('previewEnd').innerText = isoEndDate;
  }
}







function loadBrandDetails(brand_id) {
  if (!brand_id) return document.getElementById('brandDetails').innerText = "Select a brand to view details.";
  fetch(`fetch_brand.php?brand_id=${brand_id}`)
    .then(res => res.text())
    .then(data => document.getElementById('brandDetails').innerHTML = data);
}

function loadInsuranceDetails(ins_id) {
  if (!ins_id) return document.getElementById('insuranceDetails').innerText = "Select a plan to view details.";
  fetch(`fetch_insurance.php?insurance_id=${ins_id}`)
    .then(res => res.text())
    .then(data => document.getElementById('insuranceDetails').innerHTML = data);
}

function loadStaffDetails(staff_id) {
  if (!staff_id) return document.getElementById('staffDetails').innerText = "Select staff to view details.";
  fetch(`fetch_staff.php?staff_id=${staff_id}`)
    .then(res => res.text())
    .then(data => document.getElementById('staffDetails').innerHTML = data);
}




function loadCustomerDetails(cus_id) {
  if (cus_id == "") {
    document.getElementById('customerDetails').innerHTML = "Select a customer to view details.";
    return;
  }
  fetch(`fetch_customer.php?cus_id=${cus_id}`)
    .then(res => res.text())
    .then(data => document.getElementById('customerDetails').innerHTML = data);
}

function calculatePremiumAndEndDate() {
  const insurance = document.getElementById('insurance_id');
  const selectedOption = insurance.options[insurance.selectedIndex];
  const percentage = selectedOption.getAttribute('data-premium');
  const durationMonths = selectedOption.getAttribute('data-duration');
  const productValue = parseFloat(document.getElementById('product_value').value) || 0;

  // Calculate premium
  if (percentage && productValue) {
    const premium = (productValue * parseFloat(percentage)) / 100;
    document.getElementById('premium_amount').value = premium.toFixed(2);
  }

  // Auto-calculate end date when start date changes
  const startInput = document.getElementById('insurance_start');
  const startDate = new Date(startInput.value);
  if (!isNaN(startDate) && durationMonths) {
    const endDate = new Date(startDate);
    endDate.setMonth(endDate.getMonth() + parseInt(durationMonths));
    const isoEndDate = endDate.toISOString().split('T')[0];
    document.getElementById('insurance_end').value = isoEndDate;
  }
}

document.getElementById('insurance_id').addEventListener('change', calculatePremiumAndEndDate);
document.getElementById('product_value').addEventListener('input', calculatePremiumAndEndDate);
document.getElementById('insurance_start').addEventListener('change', calculatePremiumAndEndDate);
</script>
</body>
</html>
