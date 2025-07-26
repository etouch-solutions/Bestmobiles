<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Users List</title>
</head>
<body>

<a href="branch_form.html">add a branch</a> <br>
<a href="add_staff.html">add a staff</a> <br>
<a href="add_brand.html">add a brand</a> <br>
<a href="add_defect.html">add a defect</a> <br>
<a href="add_insurance.html">add an insurance</a> <br>
<a href="Insurance_Entry.php">add a insurance</a>

<form action="insert_insurance_entry.php" method="POST">
  <input type="number" name="cus_id" placeholder="Customer ID" required><br>
  <input type="number" name="brand_id" placeholder="Brand ID" required><br>
  <input type="number" name="insurance_id" placeholder="Insurance ID" required><br>
  <input type="number" name="staff_id" placeholder="Staff ID" required><br>
  <input type="text" name="product_model_name" placeholder="Model" required><br>
  <input type="text" name="imei_1" placeholder="IMEI 1"><br>
  <input type="text" name="imei_2" placeholder="IMEI 2"><br>
  <input type="number" name="product_value" placeholder="Value"><br>
  <input type="date" name="bill_date"><br>
  <input type="date" name="insurance_start"><br>
  <input type="date" name="insurance_end"><br>
  <input type="number" name="premium_amount"><br>
  <select name="product_ins_status">
    <option value="1">Active</option>
    <option value="0">Inactive</option>
  </select><br>
  <select name="insurance_status">
    <option value="1">Valid</option>
    <option value="0">Invalid</option>
  </select><br>
  <button type="submit">Add Entry</button>
</form>

<hr>
<hr>
<form action="insert_claim.php" method="POST">
  <input type="number" name="insurance_entry_id" placeholder="Insurance Entry ID" required><br>
  <input type="number" name="defect_id" placeholder="Defect ID" required><br>
  <input type="number" name="defect_value" placeholder="Defect Value" required><br>
  <input type="text" name="defect_description" placeholder="Description" required><br>
  <input type="date" name="claim_date" required><br>
  <button type="submit">Add Claim</button>
</form>

    
</body>
</html>
