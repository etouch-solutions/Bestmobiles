<!DOCTYPE html>
<html>
<head>
  <title>Add Customer</title>
  <style>
    body { font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px; }
    form { background-color: #fff; padding: 20px; border-radius: 10px; max-width: 600px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    input, select, textarea { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; }
    label { font-weight: bold; display: block; margin-bottom: 5px; }
    button { background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background-color: #218838; }
  </style>
</head>
<body>

<h2 style="text-align:center">Add Customer</h2>

<form action="insert_customer.php" method="POST" enctype="multipart/form-data">
  <label for="cus_name">Customer Name</label>
  <input type="text" id="cus_name" name="cus_name" required>

  <label for="cus_cno">Contact Number</label>
  <input type="number" id="cus_cno" name="cus_cno" required>

  <label for="cus_address">Address</label>
  <textarea id="cus_address" name="cus_address" required></textarea>

  <label for="cus_email">Email</label>
  <input type="email" id="cus_email" name="cus_email">

  <label for="cus_ref">Reference Name</label>
  <input type="text" id="cus_ref" name="cus_ref">

  <label for="cus_ref_cno">Reference Contact Number</label>
  <input type="number" id="cus_ref_cno" name="cus_ref_cno">

  <label for="branch_id">Branch ID</label>
  <select id="branch_id" name="branch_id">
    <?php
      include 'db.php';
      $conn = mysqli_connect($host, $user, $pass, $db);
      $res = mysqli_query($conn, "SELECT Branch_Id, Branch_Name FROM Branch_Master WHERE Branch_Status = 1");
      while ($row = mysqli_fetch_assoc($res)) {
        echo "<option value='{$row['Branch_Id']}'>{$row['Branch_Name']}</option>";
      }
    ?>
  </select>

  <label for="cus_photo">Customer Photo</label>
  <input type="file" id="cus_photo" name="cus_photo" accept="image/*">

  <label for="cus_id_copy">ID Copy</label>
  <input type="file" id="cus_id_copy" name="cus_id_copy" accept="image/*">

  <label for="cus_status">Status</label>
  <select id="cus_status" name="cus_status">
    <option value="1">Active</option>
    <option value="0">Inactive</option>
  </select>

  <button type="submit">Add Customer</button>
</form>

</body>
</html>
