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
        <a href="index.php"><li>Dashboard</li></a>
        <a href="branch.php" class="active"><li>Branch Master</li></a>
        <a href="brand.php"><li>Brand Master</li></a>
        <a href="add_staff.php"><li>Staff Master</li></a>
        <a href="Customer_Master.php"><li>Customer Master</li></a>
        <a href="add_insurance.php"><li>Insurance Master</li></a>
        <a href="add_defect.php"><li>Defect Master</li></a>
        <a href="insurance_entry.php"><li>Insurance Entry</li></a>
        <a href="serch.php"><li>Claim</li></a>
      </ul>
    </aside>

    <main class="main-content">
      <div class="content-area">

        <!-- Add Branch Section -->
        <section class="add-branch">
          <h3><?= $editBranch ? 'Edit Branch' : 'Add Branch' ?></h3>
          <form method="POST">
            <?php if ($editBranch): ?>
              <input type="hidden" name="branch_id" value="<?= $editBranch['Branch_Id'] ?>">
            <?php endif; ?>

            <input type="text" name="branch_name" placeholder="Branch Name" required value="<?= $editBranch['Branch_Name'] ?? '' ?>">
            <input type="text" name="branch_head_name" placeholder="Branch Head Name" required value="<?= $editBranch['Branch_Head_Name'] ?? '' ?>">
            <textarea name="branch_address" placeholder="Branch Address" required><?= $editBranch['Branch_Address'] ?? '' ?></textarea>
            <input type="number" name="branch_cno" placeholder="Contact Number" required value="<?= $editBranch['Branch_CNo'] ?? '' ?>">
            <select name="branch_status" required>
              <option value="">Select Status</option>
              <option value="1" <?= (isset($editBranch['Branch_Status']) && $editBranch['Branch_Status'] == 1) ? 'selected' : '' ?>>Active</option>
              <option value="0" <?= (isset($editBranch['Branch_Status']) && $editBranch['Branch_Status'] == 0) ? 'selected' : '' ?>>Inactive</option>
            </select>
            <button type="submit"><?= $editBranch ? 'Update Branch' : 'Add Branch' ?></button>
          </form>
        </section>

        <!-- Branch Overview Section -->
        <section class="overview">
          <h3>Branch Overview</h3>
          <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" placeholder="Search branch..." onkeyup="filterBranches(this.value)">
          </div>

          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Contact No</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $result = mysqli_query($conn, "SELECT * FROM Branch_Master ORDER BY Branch_Id DESC");
                while ($row = mysqli_fetch_assoc($result)) {
                  $statusClass = $row['Branch_Status'] ? 'active-row' : 'inactive-row';
                  echo "<tr class='$statusClass'>
                          <td>{$row['Branch_Name']}</td>
                          <td>{$row['Branch_CNo']}</td>
                          <td>" . ($row['Branch_Status'] ? 'Active' : 'Inactive') . "</td>
                          <td class='action-btns'>
                            <a href='?edit={$row['Branch_Id']}'><i class='fa fa-pen'></i></a>
                            
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

  <script>
    function filterBranches(query) {
      const rows = document.querySelectorAll("table tbody tr");
      rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(query.toLowerCase()) ? '' : 'none';
      });
    }

    function deleteBranch(id) {
      if (confirm("Are you sure you want to delete this branch?")) {
        window.location.href = "?delete=" + id;
      }
    }

    function toggleSidebar() {
      const sidebar = document.getElementById('sidebarMenu');
      sidebar.classList.toggle('mobile-visible');
      sidebar.classList.toggle('mobile-hidden');
    }
  </script>
</body>

</html>
