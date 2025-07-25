<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Add Branch</title>
</head>
<body>
  <h2>Add Branch Information</h2>
  <form action="insert_branch.php" method="POST">
    <label>Branch Name:</label><br>
    <input type="text" name="branch_name" required><br><br>

    <label>Branch Head Name:</label><br>
    <input type="text" name="branch_head_name" required><br><br>

    <label>Branch Address:</label><br>
    <textarea name="branch_address" required></textarea><br><br>

    <label>Branch Contact No:</label><br>
    <input type="number" name="branch_cno" required><br><br>

    <label>Status:</label><br>
    <select name="branch_status">
      <option value="1">Active</option>
      <option value="0">Inactive</option>
    </select><br><br>

    <input type="submit" value="Add Branch">
  </form>
</body>
</html>
