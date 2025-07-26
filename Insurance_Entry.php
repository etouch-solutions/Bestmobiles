<!DOCTYPE html>
<html>
<head>
  <title>Add Insurance Entry</title>
  <style>
    body { font-family: Arial; padding: 20px; background: #f2f2f2; }
    form { background: #fff; padding: 20px; border-radius: 10px; max-width: 600px; margin: auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    input, select { width: 100%; padding: 10px; margin: 8px 0 20px; border: 1px solid #ccc; border-radius: 5px; }
    label { font-weight: bold; }
    button { background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background: #45a049; }
  </style>
</head>
<body>

<h2 style="text-align:center">Add Insurance Entry</h2>

<form action="insert_insurance_entry.php" method="POST">
  <label for="cus_id">Select Customer</label>
  <select name="cus_id" id="cus_id">
    <?php
      include 'db.php';
      $conn = mysqli_connect($host, $user, $pass, $db);
      $res = mysqli_query($conn, "SELECT Cus_Id, Cus_Name FROM Customer_Master WHERE Cus_Status = 1");
      while ($row = mysqli_fetch_assoc($res)) {
        echo "<option value='{$row['Cus_Id']}'>{$row['Cus_Name']}</option>";
      }
    ?>
  </select>

  <label for="brand_id">Select Brand</label>
  <select name="brand_id" id="brand_id">
    <?php
      $res = mysqli_query($conn, "SELECT Brand_Id, Brand_Name FROM Brands_Master WHERE Brand_Status = 1");
      while ($row = mysqli_fetch_assoc($res)) {
        echo "<option value='{$row['Brand_Id']}'>{$row['Brand_Name']}</option>";
      }
    ?>
  </select>

  <label for="insurance_id">Select Insurance</label>
  <select name="insurance_id" id="insurance_id">
    <?php
      $res = mysqli_query($conn, "SELECT Insurance_Id, Insurance_Name FROM Insurance_Master WHERE Insurance_Status = 1");
      while ($row = mysqli_fetch_assoc($res)) {
        echo "<option value='{$row['Insurance_Id']}'>{$row['Insurance_Name']}</option>";
      }
    ?>
  </select>

  <label for="staff_id">Select Staff</label>
  <select name="staff_id" id="staff_id">
    <?php
      $res = mysqli_query($conn, "SELECT Staff_Id, Staff_Name FROM Staff_Master WHERE Staff_Status = 1");
      while ($row = mysqli_fetch_assoc($res)) {
        echo "<option value='{$row['Staff_Id']}'>{$row['Staff_Name']}</option>";
      }
    ?>
  </select>

  <label>Product Model Name:</label>
  <input type="text" name="product_model_name" required>

  <label>IMEI 1:</label>
  <input type="text" name="imei_1">

  <label>IMEI 2:</label>
  <input type="text" name="imei_2">

  <label>Product Value (₹):</label>
  <input type="number" name="product_value">

  <label>Bill Date:</label>
  <input type="date" name="bill_date">

  <label>Insurance Start Date:</label>
  <input type="date" name="insurance_start">

  <label>Insurance End Date:</label>
  <input type="date" name="insurance_end">

  <label>Premium Amount (₹):</label>
  <input type="number" name="premium_amount">

  <label>Product Insurance Status:</label>
  <select name="product_ins_status">
    <option value="1">Active</option>
    <option value="0">Inactive</option>
  </select>

  <label>Insurance Validity:</label>
  <select name="insurance_status">
    <option value="1">Valid</option>
    <option value="0">Invalid</option>
  </select>

  <button type="submit">Submit Insurance Entry</button>
</form>

</body>
</html>
