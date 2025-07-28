<!DOCTYPE html>
<html>
<head>
  <title>Add Insurance Entry</title>
  <style>
    body { font-family: Arial; background: #f2f2f2; padding: 20px; }
    .container { display: flex; justify-content: space-between; max-width: 1200px; margin: auto; }
    form, .preview { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; width: 48%; }
    label { font-weight: bold; margin-top: 10px; display: block; }
    input, select { width: 100%; padding: 8px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; }
    button { padding: 10px 20px; background: green; color: white; border: none; border-radius: 5px; cursor: pointer; }
    .preview h3 { margin-bottom: 10px; }
    .preview div { margin-bottom: 10px; }
  </style>
</head>
<body>
<h2 style="text-align:center">Add Insurance Entry</h2>
<div class="container">
  <form method="POST" action="insert_insurance_entry.php" enctype="multipart/form-data">
    <?php include 'db.php'; $conn = mysqli_connect($host, $user, $pass, $db); ?>

    <!-- Customer -->
    <label>Select Customer</label>
    <select name="cus_id" id="cus_id" onchange="updatePreview('customer')">
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
    <select name="brand_id" id="brand_id" onchange="updatePreview('brand')">
      <option value="">-- Select --</option>
      <?php
      $res = mysqli_query($conn, "SELECT Brand_Id, Brand_Name FROM Brands_Master WHERE Is_Active = 1");
      while ($row = mysqli_fetch_assoc($res)) {
        echo "<option value='{$row['Brand_Id']}' data-brand='{$row['Brand_Name']}'>{$row['Brand_Name']}</option>";
      }
      ?>
    </select>

    <!-- Insurance Plan -->
    <label>Select Insurance Plan</label>
    <select name="insurance_id" id="insurance_id" onchange="calculatePremium()">
      <option value="">-- Select --</option>
      <?php
      $res = mysqli_query($conn, "SELECT Insurance_Id, Insurance_Name, Premium_Percentage, Duration_Months FROM Insurance_Master WHERE Insurance_Status = 1");
      while ($row = mysqli_fetch_assoc($res)) {
        echo "<option value='{$row['Insurance_Id']}' data-name='{$row['Insurance_Name']}' data-percentage='{$row['Premium_Percentage']}' data-duration='{$row['Duration_Months']}'>
                {$row['Insurance_Name']}
              </option>";
      }
      ?>
    </select>

    <!-- Product Details -->
    <label>Product Model Name</label>
    <input type="text" name="product_model_name" id="product_model_name" oninput="updatePreview('model')">

    <label>IMEI 1</label>
    <input type="text" name="imei_1" id="imei_1" oninput="updatePreview('imei1')">

    <label>IMEI 2</label>
    <input type="text" name="imei_2" id="imei_2" oninput="updatePreview('imei2')">

    <label>Product Value (₹)</label>
    <input type="number" name="product_value" id="product_value" oninput="calculatePremium()">

    <label>Calculated Premium (₹)</label>
    <input type="number" name="premium_amount" id="premium_amount" readonly>

    <label>Insurance Start Date</label>
    <input type="date" name="insurance_start" id="insurance_start" onchange="calculateEndDate()">

    <label>Insurance End Date</label>
    <input type="date" name="insurance_end" id="insurance_end" readonly>

    <button type="submit">Submit</button>
  </form>

  <!-- Preview -->
  <div class="preview">
    <h3>Live Preview</h3>
    <div><strong>Customer:</strong> <span id="pv_customer"></span></div>
    <div><strong>Contact:</strong> <span id="pv_contact"></span></div>
    <div><strong>Brand:</strong> <span id="pv_brand"></span></div>
    <div><strong>Plan:</strong> <span id="pv_plan"></span></div>
    <div><strong>Model:</strong> <span id="pv_model"></span></div>
    <div><strong>IMEI 1:</strong> <span id="pv_imei1"></span></div>
    <div><strong>IMEI 2:</strong> <span id="pv_imei2"></span></div>
    <div><strong>Value:</strong> ₹<span id="pv_value"></span></div>
    <div><strong>Premium:</strong> ₹<span id="pv_premium"></span></div>
    <div><strong>Start:</strong> <span id="pv_start"></span></div>
    <div><strong>End:</strong> <span id="pv_end"></span></div>
  </div>
</div>

<script>
let selectedDuration = 0;

function updatePreview(type) {
  if (type === 'customer') {
    const sel = document.getElementById('cus_id');
    const name = sel.options[sel.selectedIndex].dataset.name || '';
    const contact = sel.options[sel.selectedIndex].dataset.contact || '';
    document.getElementById('pv_customer').innerText = name;
    document.getElementById('pv_contact').innerText = contact;
  }
  if (type === 'brand') {
    const sel = document.getElementById('brand_id');
    const brand = sel.options[sel.selectedIndex].dataset.brand || '';
    document.getElementById('pv_brand').innerText = brand;
  }
  if (type === 'model') {
    document.getElementById('pv_model').innerText = document.getElementById('product_model_name').value;
  }
  if (type === 'imei1') {
    document.getElementById('pv_imei1').innerText = document.getElementById('imei_1').value;
  }
  if (type === 'imei2') {
    document.getElementById('pv_imei2').innerText = document.getElementById('imei_2').value;
  }
}

function calculatePremium() {
  const sel = document.getElementById('insurance_id');
  const name = sel.options[sel.selectedIndex].dataset.name || '';
  const percentage = parseFloat(sel.options[sel.selectedIndex].dataset.percentage || 0);
  selectedDuration = parseInt(sel.options[sel.selectedIndex].dataset.duration || 0);
  const value = parseFloat(document.getElementById('product_value').value) || 0;

  document.getElementById('pv_plan').innerText = name;
  document.getElementById('pv_value').innerText = value.toFixed(2);

  if (value && percentage) {
    const premium = (value * percentage) / 100;
    document.getElementById('premium_amount').value = premium.toFixed(2);
    document.getElementById('pv_premium').innerText = premium.toFixed(2);
  }
  calculateEndDate();
}

function calculateEndDate() {
  const start = document.getElementById('insurance_start').value;
  if (!start || selectedDuration === 0) return;
  const startDate = new Date(start);
  startDate.setMonth(startDate.getMonth() + selectedDuration);
  const endDate = startDate.toISOString().split('T')[0];
  document.getElementById('insurance_end').value = endDate;
  document.getElementById('pv_start').innerText = document.getElementById('insurance_start').value;
  document.getElementById('pv_end').innerText = endDate;
}
</script>
</body>
</html>
