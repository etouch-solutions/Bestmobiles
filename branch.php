<!DOCTYPE html>
<html>
<head>
  <title>Branch Master</title>
  <style>
    body { font-family: Arial; background: #f2f2f2; padding: 20px; }
    .container { display: flex; gap: 20px; }
    .form-box, .list-box { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 5px #aaa; width: 50%; }
    input, select, textarea, button { padding: 8px; width: 100%; margin: 10px 0; }
    .branch-item { cursor: pointer; padding: 5px; border-bottom: 1px solid #ddd; }
    .branch-item:hover { background: #f9f9f9; }
    #branchDetails { margin-top: 20px; }
    .search-box { margin-bottom: 10px; }
  </style>
</head>
<body>
<h2>Branch Master</h2>
<div class="container">
  <!-- Form Box -->
  <div class="form-box">
    <h3>Add Branch</h3>
    <form method="POST">
      <label>Branch Name:</label>
      <input type="text" name="branch_name" required>

      <label>Branch Head Name:</label>
      <input type="text" name="branch_head_name" required>

      <label>Branch Address:</label>
      <textarea name="branch_address" required></textarea>

      <label>Branch Contact No:</label>
      <input type="number" name="branch_cno" required>

      <label>Status:</label>
      <select name="branch_status">
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>

      <button type="submit">Add Branch</button>
    </form>
  </div>

  <!-- List Box -->
  <div class="list-box">
    <h3>All Branches</h3>
    <input type="text" placeholder="Search branch..." class="search-box" onkeyup="filterBranches(this.value)">
    <div id="branchList">
      <?php
      include 'db.php';
      $conn = mysqli_connect($host, $user, $pass, $db);

      if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['branch_name'])) {
        $bname = mysqli_real_escape_string($conn, $_POST['branch_name']);
        $head = mysqli_real_escape_string($conn, $_POST['branch_head_name']);
        $addr = mysqli_real_escape_string($conn, $_POST['branch_address']);
        $cno = mysqli_real_escape_string($conn, $_POST['branch_cno']);
        $status = $_POST['branch_status'];

        mysqli_query($conn, "INSERT INTO Branch_Master (Branch_Name, Branch_Head_Name, Branch_Address, Branch_CNo, Branch_Status) VALUES ('$bname', '$head', '$addr', '$cno', '$status')");
        header("Location: branch.php");
        exit();
      }

      $result = mysqli_query($conn, "SELECT * FROM Branch_Master ORDER BY Branch_Id DESC");
      while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='branch-item' onclick='viewDetails(" . json_encode($row) . ")'>{$row['Branch_Name']}</div>";
      }
      ?>
    </div>
    <div id="branchDetails"></div>
  </div>
</div>

<script>
function filterBranches(query) {
  const items = document.querySelectorAll('.branch-item');
  items.forEach(item => {
    item.style.display = item.innerText.toLowerCase().includes(query.toLowerCase()) ? 'block' : 'none';
  });
}

function viewDetails(branch) {
  const html = `
    <h4>Branch Details</h4>
    <p><strong>ID:</strong> ${branch.Branch_Id}</p>
    <p><strong>Name:</strong> ${branch.Branch_Name}</p>
    <p><strong>Head:</strong> ${branch.Branch_Head_Name}</p>
    <p><strong>Address:</strong> ${branch.Branch_Address}</p>
    <p><strong>Contact:</strong> ${branch.Branch_CNo}</p>
    <p><strong>Status:</strong> ${branch.Branch_Status == 1 ? 'Active' : 'Inactive'}</p>
  `;
  document.getElementById("branchDetails").innerHTML = html;
}
</script>
</body>
</html>
