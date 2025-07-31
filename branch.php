<?php
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

// Insert or Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['branch_name'])) {
    $name = mysqli_real_escape_string($conn, $_POST['branch_name']);
    $head = mysqli_real_escape_string($conn, $_POST['branch_head_name']);
    $addr = mysqli_real_escape_string($conn, $_POST['branch_address']);
    $cno = mysqli_real_escape_string($conn, $_POST['branch_cno']);
    $status = intval($_POST['branch_status']);
    $branchId = $_POST['branch_id'] ?? null;

    if ($branchId) {
        $stmt = $conn->prepare("UPDATE Branch_Master SET Branch_Name=?, Branch_Head_Name=?, Branch_Address=?, Branch_CNo=?, Branch_Status=? WHERE Branch_Id=?");
        $stmt->bind_param("sssiii", $name, $head, $addr, $cno, $status, $branchId);
    } else {
        $stmt = $conn->prepare("INSERT INTO Branch_Master (Branch_Name, Branch_Head_Name, Branch_Address, Branch_CNo, Branch_Status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $name, $head, $addr, $cno, $status);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: branch.php");
    exit();
}

// Delete
if (isset($_GET['delete'])) {
    $delId = intval($_GET['delete']);
    $conn->query("DELETE FROM Branch_Master WHERE Branch_Id = $delId");
    header("Location: branch.php?deleted=1");
    exit();
}

// Fetch for edit
$editBranch = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM Branch_Master WHERE Branch_Id = $editId");
    if ($res && $res->num_rows > 0) {
        $editBranch = $res->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Branch Master</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="navtop">
    
    <div class="logo">LOGO</div>
    <h1> Best Mobile Insurance Software</h1>
    <div class="hamburger" onclick="toggleSidebar()">☰</div>
  </div>

   <div class="container">
    <aside class="sidebar mobile-hidden" id="sidebarMenu">
      <ul>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="index.php"><li>Dashboard</li></a>
     <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;" href="branch.php" class="active"> <li>Branch Master</li></a>
     <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="brand.php" > <li>Brand Master</li></a>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="add_staff.php"><li>Staff Master</li></a>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="Customer_Master.php"><li>Customer Master</li></a>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="add_insurance.php"><li>Insurance Master</li></a>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="add_defect.php"><li>Defect Master</li></a>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="insurance_entry.php"><li>Insurance Entry</li></a>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="serch.php"><li>Claim</li></a>
      </ul>
    </aside>

      <div class="form-box">
    <h3><?= $editBranch ? 'Edit Branch' : 'Add Branch' ?></h3>
    <form method="POST">
      <?php if ($editBranch): ?>
        <input type="hidden" name="branch_id" value="<?= $editBranch['Branch_Id'] ?>">
      <?php endif; ?>

      <label>Branch Name:</label>
      <input type="text" name="branch_name" required value="<?= $editBranch['Branch_Name'] ?? '' ?>">

      <label>Branch Head Name:</label>
      <input type="text" name="branch_head_name" required value="<?= $editBranch['Branch_Head_Name'] ?? '' ?>">

      <label>Branch Address:</label>
      <textarea name="branch_address" required><?= $editBranch['Branch_Address'] ?? '' ?></textarea>

      <label>Branch Contact No:</label>
      <input type="number" name="branch_cno" required value="<?= $editBranch['Branch_CNo'] ?? '' ?>">

      <label>Status:</label>
      <select name="branch_status">
        <option value="1" <?= (isset($editBranch['Branch_Status']) && $editBranch['Branch_Status'] == 1) ? 'selected' : '' ?>>Active</option>
        <option value="0" <?= (isset($editBranch['Branch_Status']) && $editBranch['Branch_Status'] == 0) ? 'selected' : '' ?>>Inactive</option>
      </select>

      <button type="submit"><?= $editBranch ? 'Update Branch' : 'Add Branch' ?></button>
    </form>
  </div>
</div>
 <main class="main-content">
      <div class="content-area">
  <!-- Form Box -->
  <div class="form-box">
    <h3><?= $editBranch ? 'Edit Branch' : 'Add Branch' ?></h3>
    <form method="POST">
      <?php if ($editBranch): ?>
        <input type="hidden" name="branch_id" value="<?= $editBranch['Branch_Id'] ?>">
      <?php endif; ?>

      <label>Branch Name:</label>
      <input type="text" name="branch_name" required value="<?= $editBranch['Branch_Name'] ?? '' ?>">

      <label>Branch Head Name:</label>
      <input type="text" name="branch_head_name" required value="<?= $editBranch['Branch_Head_Name'] ?? '' ?>">

      <label>Branch Address:</label>
      <textarea name="branch_address" required><?= $editBranch['Branch_Address'] ?? '' ?></textarea>

      <label>Branch Contact No:</label>
      <input type="number" name="branch_cno" required value="<?= $editBranch['Branch_CNo'] ?? '' ?>">

      <label>Status:</label>
      <select name="branch_status">
        <option value="1" <?= (isset($editBranch['Branch_Status']) && $editBranch['Branch_Status'] == 1) ? 'selected' : '' ?>>Active</option>
        <option value="0" <?= (isset($editBranch['Branch_Status']) && $editBranch['Branch_Status'] == 0) ? 'selected' : '' ?>>Inactive</option>
      </select>

      <button type="submit"><?= $editBranch ? 'Update Branch' : 'Add Branch' ?></button>
    </form>
  </div>

  <!-- List Box -->
  <div class="list-box">
    <h3>All Branches</h3>
    <input type="text" placeholder="Search branch..." class="search-box" onkeyup="filterBranches(this.value)">
    <div id="branchList">
      <?php
      $result = mysqli_query($conn, "SELECT * FROM Branch_Master ORDER BY Branch_Id DESC");
      while ($row = mysqli_fetch_assoc($result)) {
        $jsonRow = json_encode($row);
        echo "<div class='branch-item' onclick='viewDetails($jsonRow)'>
                {$row['Branch_Name']}
                <div class='actions'>
                  <a href='?edit={$row['Branch_Id']}'>Edit</a>
                  <a href='javascript:void(0)' class='delete' onclick='deleteBranch({$row['Branch_Id']})'>Delete</a>
                </div>
              </div>";
      }
      ?>
    </div>
    <div id="branchDetails"></div>
</div>
    </main>

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

function deleteBranch(id) {
  if (confirm("Are you sure you want to delete this branch?")) {
    window.location.href = "?delete=" + id;
  }
}
</script>
</body>
</html>
