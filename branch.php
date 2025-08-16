<?php
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load PhpSpreadsheet
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// =================== INSERT / UPDATE ===================
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

// =================== DELETE ===================
if (isset($_GET['delete'])) {
    $delId = intval($_GET['delete']);
    $conn->query("DELETE FROM Branch_Master WHERE Branch_Id = $delId");
    header("Location: branch.php?deleted=1");
    exit();
}

// =================== FETCH FOR EDIT ===================
$editBranch = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM Branch_Master WHERE Branch_Id = $editId");
    if ($res && $res->num_rows > 0) {
        $editBranch = $res->fetch_assoc();
    }
}

// =================== EXPORT TO EXCEL ===================
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Headers
    $sheet->setCellValue('A1', 'Branch Name');
    $sheet->setCellValue('B1', 'Branch Head');
    $sheet->setCellValue('C1', 'Address');
    $sheet->setCellValue('D1', 'Contact No');
    $sheet->setCellValue('E1', 'Status');

    // Fetch Data
    $result = mysqli_query($conn, "SELECT * FROM Branch_Master ORDER BY Branch_Id DESC");
    $rowCount = 2;
    while ($row = mysqli_fetch_assoc($result)) {
        $sheet->setCellValue("A$rowCount", $row['Branch_Name']);
        $sheet->setCellValue("B$rowCount", $row['Branch_Head_Name']);
        $sheet->setCellValue("C$rowCount", $row['Branch_Address']);
        $sheet->setCellValue("D$rowCount", $row['Branch_CNo']);
        $sheet->setCellValue("E$rowCount", $row['Branch_Status'] ? "Active" : "Inactive");
        $rowCount++;
    }

    // Output File
    $writer = new Xlsx($spreadsheet);
    $filename = "branches.xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $writer->save("php://output");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Branch Master</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .modal { display:none; position:fixed; z-index:1000; padding-top:60px; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.6);}
    .modal-content { background:#fff; margin:auto; padding:20px; border-radius:10px; width:400px; position:relative;}
    .close { position:absolute; top:10px; right:15px; font-size:22px; color:red; cursor:pointer;}
    .modal-content h4 { margin-top:0;}
  </style>
</head>
<body>
  <div class="navtop">
    <div class="logo">LOGO</div>
    <h1>Best Mobile Insurance Software</h1>
    <div class="hamburger" onclick="toggleSidebar()">☰</div>
    <a href="?export=excel" class="btn" style="background:#28a745;color:white;padding:8px 12px;border-radius:5px;text-decoration:none;">
      ⬇ Download Excel
    </a>
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

        <!-- Add/Edit Branch -->
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
              <option value="1" <?= (isset($editBranch['Branch_Status']) && $editBranch['Branch_Status'] == 1) ? 'selected' : '' ?>>Active</option>
              <option value="0" <?= (isset($editBranch['Branch_Status']) && $editBranch['Branch_Status'] == 0) ? 'selected' : '' ?>>Inactive</option>
            </select>
            <button type="submit"><?= $editBranch ? 'Update Branch' : 'Add Branch' ?></button>
          </form>
        </section>

        <!-- Branch Overview -->
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
                  $json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                  $statusClass = $row['Branch_Status'] ? 'active-row' : 'inactive-row';
                  echo "<tr class='$statusClass'>
                          <td>{$row['Branch_Name']}</td>
                          <td>{$row['Branch_CNo']}</td>
                          <td>" . ($row['Branch_Status'] ? 'Active' : 'Inactive') . "</td>
                          <td class='action-btns'>
                            <a href='javascript:void(0)' onclick='viewBranch($json)'><i class='fa fa-eye'></i></a>
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

  <!-- Modal -->
  <div id="branchModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h4>Branch Details</h4>
      <div id="modalDetails"></div>
    </div>
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

    function viewBranch(branch) {
      const html = `
        <p><strong>Branch Name:</strong> ${branch.Branch_Name}</p>
        <p><strong>Head Name:</strong> ${branch.Branch_Head_Name}</p>
        <p><strong>Address:</strong> ${branch.Branch_Address}</p>
        <p><strong>Contact No:</strong> ${branch.Branch_CNo}</p>
        <p><strong>Status:</strong> ${branch.Branch_Status == 1 ? 'Active' : 'Inactive'}</p>
      `;
      document.getElementById('modalDetails').innerHTML = html;
      document.getElementById('branchModal').style.display = 'block';
    }

    function closeModal() {
      document.getElementById('branchModal').style.display = 'none';
    }

    window.onclick = function(event) {
      const modal = document.getElementById('branchModal');
      if (event.target == modal) {
        closeModal();
      }
    }
  </script>
</body>
</html>
