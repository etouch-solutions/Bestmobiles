<!DOCTYPE html>
<html>
<head>
  <title>Claim Entry</title>
  <style>
    body { font-family: Arial; background: #f5f5f5; padding: 30px; }
    .container { max-width: 650px; margin: auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
    input, select, textarea { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; }
    button { background: green; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background: darkgreen; }
    .info-box { background: #f1f1f1; padding: 15px; margin-bottom: 20px; border-left: 5px solid green; }
    label { font-weight: bold; }
  </style>
</head>
<body>
<div class="container">
  <h2>Claim Entry</h2>

  <?php if ($info): ?>
    <div class="info-box">
      <b>Name:</b> <?= $info['Cus_Name'] ?> | <b>Phone:</b> <?= $info['Cus_CNo'] ?><br>
      <b>Model:</b> <?= $info['Product_Model_Name'] ?> | <b>IMEI:</b> <?= $info['IMEI_1'] ?><br>
      <b>Plan:</b> <?= $info['Insurance_Name'] ?><br>
      <b>Product Value:</b> ₹<?= $info['Product_Value'] ?> <br>
      <b>Premium:</b> ₹<?= $info['Premium_Amount'] ?><br>
      <b>Period:</b> <?= $info['Insurance_Start_Date'] ?> → <?= $info['Insurance_End_Date'] ?>
    </div>

    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="insurance_entry_id" value="<?= $insurance_id ?>">

      <label>Select Defect</label>
      <select name="defect_id" required>
        <?php
          $res = mysqli_query($conn, "SELECT Defect_Id, Defect_Name FROM Defect_Master WHERE Is_Active = 1");
          if ($res && mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
              echo "<option value='{$row['Defect_Id']}'>{$row['Defect_Name']}</option>";
            }
          } else {
            echo "<option disabled>No defects found</option>";
          }
        ?>
      </select> 

      <!-- ✅ NEW FIELD FOR DEFECT VALUE -->
      <label>Defect Value (₹)</label>
      <input type="number" name="defect_value" min="0" step="0.01" required>

      <label>Remarks (optional)</label>
      <textarea name="remarks" rows="3"></textarea>

      <label>Upload Image</label>
      <input type="file" name="claim_image" accept="image/*" required>

      <button type="submit">Submit Claim</button>
    </form>
  <?php else: ?>
    <p style="color: red;">Invalid insurance ID.</p>
  <?php endif; ?>
</div>
</body>
</html>
