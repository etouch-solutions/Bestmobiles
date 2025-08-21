<?php include 'db.php'; $conn = mysqli_connect($host, $user, $pass, $db); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Add Insurance Entry</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="navtop">
  <div class="logo">LOGO</div>
  <h1><span>➕</span> Add Insurance Entry</h1>
  <div class="hamburger" onclick="toggleSidebar()">☰</div>
</div>

<div class="container">
 <aside class="sidebar mobile-hidden" id="sidebarMenu">
      <ul>
        <a href="index.php"><li>Dashboard</li></a>
        <a href="branch.php" class="active"><li>Branch Master</li></a>
        <a href="brand.php"><li>Brand Master</li></a>
        <a href="add_staff.php"><li>Staff Master</li></a>
        <a href="Customer_Master.php"><li>Customer Master</li></a>
        <a href="add_insurance.php"><li>Insurance Master</li></a>
        <a href="add_defect.php"><li>Defect Master</li></a>
        <a href="insuranceentry.php"><li>Insurance Entry</li></a>
        <a href="serch.php"><li>Claim</li></a>
      </ul>
    </aside>

  <main class="main-content">
    <div class="content-area">
      <!-- Form Section -->
      <section class="add-branch">
        <h3>Add Insurance Entry</h3>
        <form action="insert_insurance_entry.php" method="POST" enctype="multipart/form-data">
          <label>Select Customer</label>
          <select name="cus_id" onchange="loadCustomerDetails(this.value)" required>
            <option value="">-- Select --</option>
            <?php
              $res = mysqli_query($conn, "SELECT Cus_Id, Cus_Name FROM Customer_Master WHERE Is_Active=1");
              while ($row = mysqli_fetch_assoc($res))
                echo "<option value='{$row['Cus_Id']}'>{$row['Cus_Name']}</option>";
            ?>
          </select>

          <label>Select Brand</label>
          <select name="brand_id" onchange="loadBrandDetails(this.value)" required>
            <option value="">-- Select --</option>
            <?php
              $res = mysqli_query($conn, "SELECT Brand_Id, Brand_Name FROM Brands_Master WHERE Is_Active=1");
              while ($row = mysqli_fetch_assoc($res))
                echo "<option value='{$row['Brand_Id']}'>{$row['Brand_Name']}</option>";
            ?>
          </select>

          <label>Select Insurance Plan</label>
          <select name="insurance_id" id="insurance_id" onchange="calculatePremiumAndEndDate(); loadInsuranceDetails(this.value)" required>
            <option value="">-- Select --</option>
            <?php
             $res = mysqli_query($conn, "SELECT Insurance_Id, Insurance_Name, Premium_Percentage, Duration_Months 
                            FROM Insurance_Master 
                            WHERE Insurance_Status=1");

              while($r = mysqli_fetch_assoc($res))
                echo "<option value='{$r['Insurance_Id']}' data-premium='{$r['Premium_Percentage']}' data-duration='{$r['Duration_Months']}'>{$r['Insurance_Name']}</option>";
            ?>
          </select>

          <label>Select Staff</label>
          <select name="staff_id" required>
            <option value="">-- Select --</option>
            <?php
              $res = mysqli_query($conn, "SELECT Staff_Id, Staff_Name FROM Staff_Master WHERE Staff_Status=1");
              while($r = mysqli_fetch_assoc($res))
                echo "<option value='{$r['Staff_Id']}'>{$r['Staff_Name']}</option>";
            ?>
          </select>

          <label>Product Model Name</label>
          <input type="text" name="product_model_name" oninput="updatePreview('model', this)" required>

          <label>IMEI 1</label>
          <input type="text" name="imei_1" oninput="updatePreview('imei1', this)" required>

          <label>IMEI 2</label>
          <input type="text" name="imei_2" oninput="updatePreview('imei2', this)">

          <label>Product Value (₹)</label>
          <input type="number" name="product_value" id="product_value" oninput="premiumEditedManually=false; calculatePremiumAndEndDate(); updatePreview('value', this)" required>

          <label>Calculated Premium (₹)</label>
          <input type="number" name="premium_amount" id="premium_amount" oninput="manualPremiumEdit(this)">

          <label>Upload Product Photo</label>
          <input type="file" name="product_photo" accept="image/*" onchange="previewImage(this,'previewProductPhoto')">

          <label>Upload Bill Photo</label>
          <input type="file" name="bill_photo" accept="image/*" onchange="previewImage(this,'previewBillPhoto')">

          <label>Bill Date</label>
          <input type="date" name="bill_date" id="bill_date" onchange="setStartDateFromBill(this.value)" required>

          <label>Insurance Start</label>
          <input type="date" name="insurance_start" id="insurance_start" onchange="premiumEditedManually=false; calculatePremiumAndEndDate(); updatePreview('start', this)" required>

          <label>Insurance End</label>
          <input type="date" name="insurance_end" id="insurance_end" onchange="updatePreview('end', this)">

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
      </section>

      <!-- Preview Panel -->
      <section class="overview">
        <h3>Live Preview</h3>
        <div id="customerDetails">Select a customer to view details.</div>
        <div id="brandDetails">Select a brand to view details.</div>
        <div id="insuranceDetails">Select a plan to view details.</div>
        <p id="previewModel">Model: -</p>
        <p id="previewIMEI1">IMEI 1: -</p>
        <p id="previewIMEI2">IMEI 2: -</p>
        <p id="previewValue">Product Value: -</p>
        <p id="previewPremium">Premium: -</p>
        <p id="previewBillDate">Bill Date: -</p>
        <p id="previewStart">Start Date: -</p>
        <p id="previewEnd">End Date: -</p>
        <p id="previewInsStatus">Insurance Status: -</p>
        <p id="previewProdStatus">Product Insurance Status: -</p>
        <div>Product Photo:<br><img id="previewProductPhoto" width="120"></div>
        <div>Bill Photo:<br><img id="previewBillPhoto" width="120"></div>
      </section>
    </div>
  </main>
</div>

<!-- JS -->
<script>
function toggleSidebar(){document.getElementById('sidebarMenu').classList.toggle('mobile-hidden');}
function updatePreview(type, el){
  const val = el.value || '-';
  const map = {model:'previewModel', imei1:'previewIMEI1', imei2:'previewIMEI2', value:'previewValue', bill:'previewBillDate', start:'previewStart', end:'previewEnd', insStatus:'previewInsStatus', prodStatus:'previewProdStatus'};
  document.getElementById(map[type]).innerText = (type==='insStatus'||type==='prodStatus') ? el.options[el.selectedIndex].text : val;
}
function previewImage(inp, id){
  if(!inp.files[0])return;
  const r=new FileReader();
  r.onload=e=>document.getElementById(id).src=e.target.result;
  r.readAsDataURL(inp.files[0]);
}

// --- Premium Logic ---
let premiumEditedManually = false;

// If user edits premium manually
function manualPremiumEdit(el) {
  premiumEditedManually = true;
  document.getElementById('previewPremium').innerText = '₹' + (el.value || 0);
}

// Auto set Insurance Start when Bill Date is selected
function setStartDateFromBill(billDate) {
  if (!billDate) return;
  document.getElementById('insurance_start').value = billDate;
  updatePreview('start', document.getElementById('insurance_start'));
  premiumEditedManually = false;
  calculatePremiumAndEndDate();
}

// Auto-calc Premium & End Date
function calculatePremiumAndEndDate() {
  const ins = document.getElementById('insurance_id');
  const opt = ins.options[ins.selectedIndex];
  if (!opt) return;

  const premium = opt.getAttribute('data-premium');
  const duration = opt.getAttribute('data-duration');
  const productValue = parseFloat(document.getElementById('product_value').value) || 0;

  // Premium Calculation (only if not edited manually)
  if (!premiumEditedManually && premium && productValue) {
    const pr = (productValue * parseFloat(premium)) / 100;
    document.getElementById('premium_amount').value = pr.toFixed(2);
    document.getElementById('previewPremium').innerText = '₹' + pr.toFixed(2);
  }

  // Insurance End Date Calculation
  const startDate = document.getElementById('insurance_start').value;
  if (startDate && duration) {
    const dt = new Date(startDate);
    dt.setMonth(dt.getMonth() + parseInt(duration));

    const iso = dt.toISOString().split('T')[0];
    document.getElementById('insurance_end').value = iso;
    document.getElementById('previewEnd').innerText = iso;
  }
}

function loadCustomerDetails(id){ if(!id)return document.getElementById('customerDetails').innerText="Select a customer to view details.";fetch(`fetch_customer.php?cus_id=${id}`).then(r=>r.text()).then(d=>document.getElementById('customerDetails').innerHTML=d);}
function loadBrandDetails(id){ if(!id)return document.getElementById('brandDetails').innerText="Select a brand to view details.";fetch(`fetch_brand.php?brand_id=${id}`).then(r=>r.text()).then(d=>document.getElementById('brandDetails').innerHTML=d);}
function loadInsuranceDetails(id){ if(!id)return document.getElementById('insuranceDetails').innerText="Select a plan to view details.";fetch(`fetch_insurance.php?insurance_id=${id}`).then(r=>r.text()).then(d=>document.getElementById('insuranceDetails').innerHTML=d);}

</script>

</body>
</html>
 