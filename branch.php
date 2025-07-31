<?php
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

// Insert or Update Branch
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

// Delete Branch
if (isset($_GET['delete'])) {
    $delId = intval($_GET['delete']);
    $conn->query("DELETE FROM Branch_Master WHERE Branch_Id = $delId");
    header("Location: branch.php?deleted=1");
    exit();
}

// Edit Branch
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
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
   <div class="navtop">
    <div class="logo">LOGO</div>
    <h1>Best Mobile Insurance Software</h1>
    <div class="hamburger" onclick="toggleSidebar()">☰</div>
  </div>

<div class="container">
    <aside class="sidebar mobile-hidden" id="sidebarMenu">
      <ul>
        <<a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="index.php"><li>Dashboard</li></a>
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

    <main class="main-content">
      <div class="content-area">
        <!-- Brand Form -->
        <section class="add-branch">
          <h3><?= $editBrand ? 'Edit Brand' : 'Add Brand' ?></h3>
          <form method="POST">
            <?php if ($editBrand): ?>
              <input type="hidden" name="brand_id" value="<?= $editBrand['Brand_Id'] ?>">
            <?php endif; ?>
            <input type="text" name="brand_name" id="brandName" placeholder="Brand Name" required value="<?= $editBrand['Brand_Name'] ?? '' ?>">
            <select name="is_active" id="status">
              <option value="">Select Status</option>
              <option value="1" <?= (isset($editBrand['Is_Active']) && $editBrand['Is_Active'] == 1) ? 'selected' : '' ?>>Active</option>
              <option value="0" <?= (isset($editBrand['Is_Active']) && $editBrand['Is_Active'] == 0) ? 'selected' : '' ?>>Inactive</option>
            </select>
            <button type="submit"><?= $editBrand ? 'Update Brand' : 'Add Brand' ?></button>
          </form>
        </section>

        <!-- Brand Overview -->
        <section class="overview">
          <h3>Brand Overview</h3>
          <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="brandSearch" placeholder="Search by name..." onkeyup="filterBrands()" />
          </div>

          <div class="table-responsive">
            <table id="brandTable">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $res = mysqli_query($conn, "SELECT * FROM Brands_Master ORDER BY Brand_Id DESC");
                  while ($row = mysqli_fetch_assoc($res)) {
                    $statusText = $row['Is_Active'] == 1 ? 'Active' : 'Inactive';
                    $statusClass = $row['Is_Active'] == 1 ? 'active-row' : 'inactive-row';
                    echo "<tr class='$statusClass'>
                            <td>{$row['Brand_Name']}</td>
                            <td>$statusText</td>
                            <td class='action-btns'>
                              <a href='?edit={$row['Brand_Id']}'><i class='fa fa-pen'></i></a>
                              <i class='fa fa-eye' onclick='viewDetails(" . json_encode($row) . ")'></i>
                              <i class='fa fa-trash' onclick='deleteBrand({$row['Brand_Id']})'></i>
                            </td>
                          </tr>";
                  }
                ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </main>
  </div>

<div class="container">
  <h2><?= $editBranch ? 'Edit Branch' : 'Add New Branch' ?></h2>
  <form method="POST" class="branch-form">
    <?php if ($editBranch): ?>
      <input type="hidden" name="branch_id" value="<?= $editBranch['Branch_Id'] ?>">
    <?php endif; ?>
    <label>Branch Name:</label>
    <input type="text" name="branch_name" required value="<?= $editBranch['Branch_Name'] ?? '' ?>">

    <label>Branch Head Name:</label>
    <input type="text" name="branch_head_name" required value="<?= $editBranch['Branch_Head_Name'] ?? '' ?>">

    <label>Branch Address:</label>
    <textarea name="branch_address" required><?= $editBranch['Branch_Address'] ?? '' ?></textarea>

    <label>Contact No:</label>
    <input type="text" name="branch_cno" required value="<?= $editBranch['Branch_CNo'] ?? '' ?>">

    <label>Status:</label>
    <select name="branch_status">
      <option value="1" <?= (isset($editBranch['Branch_Status']) && $editBranch['Branch_Status'] == 1) ? 'selected' : '' ?>>Active</option>
      <option value="0" <?= (isset($editBranch['Branch_Status']) && $editBranch['Branch_Status'] == 0) ? 'selected' : '' ?>>Inactive</option>
    </select>

    <button type="submit"><?= $editBranch ? 'Update' : 'Add' ?> Branch</button>
  </form>

  <hr>

  <h2>Branch List</h2>
  <input type="text" id="searchBox" placeholder="Search Branch" onkeyup="filterBranches(this.value)">
  <div class="branch-list" id="branchList">
    <?php
    $result = mysqli_query($conn, "SELECT * FROM Branch_Master ORDER BY Branch_Id DESC");
    while ($row = mysqli_fetch_assoc($result)) {
        $jsonRow = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
        echo "<div class='branch-item' onclick='viewDetails($jsonRow)'>
                <div>
                    <strong>{$row['Branch_Name']}</strong><br>
                    Head: {$row['Branch_Head_Name']}
                </div>
                <div>
                    <a href='?edit={$row['Branch_Id']}'>Edit</a> |
                    <a href='javascript:void(0)' onclick='deleteBranch({$row['Branch_Id']})' style='color:red;'>Delete</a>
                </div>
              </div>";
    }
    ?>
  </div>

  <div id="branchDetails" class="branch-details"></div>
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
    <h3>Branch Details</h3>
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
