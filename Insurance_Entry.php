<!DOCTYPE html>
<html>
<head>
  <title>Insurance Entry System</title>
  <style>
    body { font-family: Arial, sans-serif; background: #eef2f3; padding: 20px; }
    .container { display: flex; gap: 20px; }
    form { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 50%; }
    .preview { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 50%; }
    input, select { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; }
    label { font-weight: bold; }
    button { padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background: #218838; }
    img { max-width: 100%; height: auto; margin-top: 10px; }
    .line { margin: 10px 0; border-bottom: 1px solid #ccc; }
  </style>
</head>
<body>

<h2>Insurance Entry Form</h2>
<div class="container">
  <form id="insuranceForm" enctype="multipart/form-data">
    <?php include 'db.php'; $conn = mysqli_connect($host, $user, $pass, $db); ?>

    <label>Customer</label>
    <select name="cus_id" id="cus_id" onchange="fetchCustomerDetails(this.value)" required>
      <option value="">Select Customer</option>
      <?php
        $res = mysqli_query($conn, "SELECT Cus_Id, Cus_Name FROM Customer_Master WHERE Is_Active = 1");
        while ($row = mysqli_fetch_assoc($res)) {
          echo "<option value='{$row['Cus_Id']}'>{$row['Cus_Name']}</option>";
        }
      ?>
    </select>

    <label for="brand_id">Select Brand</label>
<select name="brand_id" id="brand_id" required>
  <?php
    include 'db.php';
    $conn = mysqli_connect($host, $user, $pass, $db);
    $res = mysqli_query($conn, "SELECT Brand_Id, Brand_Name FROM Brands_Master WHERE Brand_Status = 1");

    if (!$res) {
      echo "<option disabled>Error: " . mysqli_error($conn) . "</option>";
    } elseif (mysqli_num_rows($res) == 0) {
      echo "<option disabled>No active brands found</option>";
    } else {
      while ($row = mysqli_fetch_assoc($res)) {
        echo "<option value='{$row['Brand_Id']}'>{$row['Brand_Name']}</option>";
      }
    }
  ?>
</select>


    <label>Insurance Plan</label>
    <select name="insurance_id" id="insurance_id" onchange="handleInsuranceChange()">
      <option value="">Select Plan</option>
      <?php
        $res = mysqli_query($conn, "SELECT Insurance_Id, Insurance_Name, Premium_Percentage, Duration FROM Insurance_Master WHERE Insurance_Status = 1");
        while ($row = mysqli_fetch_assoc($res)) {
          $data = json_encode(["percentage" => $row['Premium_Percentage'], "duration" => $row['Duration']]);
          echo "<option value='{$row['Insurance_Id']}' data-info='{$data}'>{$row['Insurance_Name']}</option>";
        }
      ?>
    </select>

    <label>Staff</label>
    <select name="staff_id" id="staff_id">
      <option value="">Select Staff</option>
      <?php
        $res = mysqli_query($conn, "SELECT Staff_Id, Staff_Name FROM Staff_Master WHERE Is_Active = 1");
        while ($row = mysqli_fetch_assoc($res)) {
          echo "<option value='{$row['Staff_Id']}'>{$row['Staff_Name']}</option>";
        }
      ?>
    </select>

    <label>Product Model</label>
    <input type="text" name="product_model_name" id="product_model_name" required>

    <label>IMEI 1</label>
    <input type="text" name="imei_1" id="imei_1">

    <label>IMEI 2</label>
    <input type="text" name="imei_2" id="imei_2">

    <label>Product Value (₹)</label>
    <input type="number" name="product_value" id="product_value" oninput="calculatePremium()">

    <label>Premium Amount (Auto)</label>
    <input type="number" name="premium_amount" id="premium_amount" readonly>

    <label>Bill Date</label>
    <input type="date" name="bill_date" id="bill_date">

    <label>Start Date</label>
    <input type="date" name="insurance_start" id="insurance_start" onchange="autoCalculateEndDate()">

    <label>End Date (Auto)</label>
    <input type="date" name="insurance_end" id="insurance_end" readonly>

    <label>Bill Photo</label>
    <input type="file" name="bill_photo" accept="image/*" onchange="previewImage(event, 'bill_preview')">
    <img id="bill_preview" src="#" alt="Bill Preview" style="display:none;">

    <label>Product Photo</label>
    <input type="file" name="product_photo" accept="image/*" onchange="previewImage(event, 'product_preview')">
    <img id="product_preview" src="#" alt="Product Preview" style="display:none;">

    <button type="submit">Submit</button>
  </form>

  <div class="preview" id="previewBox">
    <h3>Live Preview</h3>
    <div class="line"></div>
    <div id="previewContent">Select a customer and enter values to see preview...</div>
    <button onclick="window.print()">Print</button>
  </div>
</div>

<script>
  let selectedInsurance = {};

  function fetchCustomerDetails(cusId) {
    fetch('get_customer_details.php?cus_id=' + cusId)
      .then(res => res.json())
      .then(data => {
        updatePreview('Customer Name: ' + data.name + '<br>Contact: ' + data.contact + '<br>Address: ' + data.address);
      });
  }

  function handleInsuranceChange() {
    const select = document.getElementById("insurance_id");
    const selected = select.options[select.selectedIndex];
    selectedInsurance = JSON.parse(selected.getAttribute("data-info"));
    calculatePremium();
    autoCalculateEndDate();
  }

  function calculatePremium() {
    const value = parseFloat(document.getElementById("product_value").value);
    if (value && selectedInsurance.percentage) {
      const premium = (value * selectedInsurance.percentage / 100).toFixed(2);
      document.getElementById("premium_amount").value = premium;
    }
    updateLivePreview();
  }

  function autoCalculateEndDate() {
    const startDate = document.getElementById("insurance_start").value;
    if (!startDate || !selectedInsurance.duration) return;

    const date = new Date(startDate);
    const match = selectedInsurance.duration.match(/(\d+)/);
    if (match) {
      date.setMonth(date.getMonth() + parseInt(match[0]));
      document.getElementById("insurance_end").value = date.toISOString().split('T')[0];
    }
    updateLivePreview();
  }

  function previewImage(event, id) {
    const img = document.getElementById(id);
    img.src = URL.createObjectURL(event.target.files[0]);
    img.style.display = 'block';
  }

  function updatePreview(html) {
    document.getElementById("previewContent").innerHTML = html;
  }

  function updateLivePreview() {
    const preview = `
      <strong>Model:</strong> ${document.getElementById('product_model_name').value}<br>
      <strong>IMEI 1:</strong> ${document.getElementById('imei_1').value}<br>
      <strong>IMEI 2:</strong> ${document.getElementById('imei_2').value}<br>
      <strong>Value:</strong> ₹${document.getElementById('product_value').value}<br>
      <strong>Premium:</strong> ₹${document.getElementById('premium_amount').value}<br>
      <strong>Start:</strong> ${document.getElementById('insurance_start').value}<br>
      <strong>End:</strong> ${document.getElementById('insurance_end').value}<br>
    `;
    updatePreview(preview);
  }
</script>

</body>
</html>
