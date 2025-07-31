<?php
include 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = mysqli_connect($host, $user, $pass, $db);

// Insert or Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['branch_name'])) {
    $branchName = mysqli_real_escape_string($conn, $_POST['branch_name']);
    $isActive = intval($_POST['is_active']);
    $branchId = $_POST['branch_id'] ?? null;

    if ($branchId) {
        $stmt = $conn->prepare("UPDATE Branch_Master SET Branch_Name = ?, Is_Active = ? WHERE Branch_Id = ?");
        $stmt->bind_param("sii", $branchName, $isActive, $branchId);
    } else {
        $stmt = $conn->prepare("INSERT INTO Branch_Master (Branch_Name, Is_Active) VALUES (?, ?)");
        $stmt->bind_param("si", $branchName, $isActive);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: branch.php");
    exit();
}

// Delete
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    $conn->query("DELETE FROM Branch_Master WHERE Branch_Id = $deleteId");
    header("Location: branch.php?deleted=1");
    exit();
}

// Edit fetch
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
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Branch Master</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
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
        <!-- Branch Form -->
        <section class="add-branch">
          <h3><?= $editBranch ? 'Edit Branch' : 'Add Branch' ?></h3>
          <form method="POST">
            <?php if ($editBranch): ?>
              <input type="hidden" name="branch_id" value="<?= $editBranch['Branch_Id'] ?>">
            <?php endif; ?>
            <input type="text" name="branch_name" placeholder="Branch Name" required value="<?= $editBranch['Branch_Name'] ?? '' ?>">
            <select name="is_active" required>
              <option value="">Select Status</option>
              <option value="1" <?= (isset($editBranch['Is_Active']) && $editBranch['Is_Active'] == 1) ? 'selected' : '' ?>>Active</option>
              <option value="0" <?= (isset($editBranch['Is_Active']) && $editBranch['Is_Active'] == 0) ? 'selected' : '' ?>>Inactive</option>
            </select>
            <button type="submit"><?= $editBranch ? 'Update Branch' : 'Add Branch' ?></button>
          </form>
        </section>

        <!-- Branch Overview -->
        <section class="overview">
          <h3>Branch Overview</h3>
          <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="branchSearch" placeholder="Search by name..." onkeyup="filterBranches()" />
          </div>

          <div class="table-responsive">
            <table id="branchTable">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $res = mysqli_query($conn, "SELECT * FROM Branch_Master ORDER BY Branch_Id DESC");
                  while ($row = mysqli_fetch_assoc($res)) {
                    $statusText = $row['Is_Active'] == 1 ? 'Active' : 'Inactive';
                    $statusClass = $row['Is_Active'] == 1 ? 'active-row' : 'inactive-row';
                    echo "<tr class='$statusClass'>
                            <td>{$row['Branch_Name']}</td>
                            <td>$statusText</td>
                            <td class='action-btns'>
                              <a href='?edit={$row['Branch_Id']}'><i class='fa fa-pen'></i></a>
                              <i class='fa fa-eye' onclick='viewDetails(" . json_encode($row) . ")'></i>
                              <i class='fa fa-trash' onclick='deleteBranch({$row['Branch_Id']})'></i>
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

  <!-- Popup for Branch Details -->
  <div class="popup-overlay" id="popupOverlay">
    <div class="popup-content" id="popupContent">
      <span class="close-btn" onclick="closePopup()">&times;</span>
      <h3>Branch Details</h3>
      <div id="popupDetails"></div>
    </div>
  </div>

  <script>
    function filterBranches() {
      const input = document.getElementById('branchSearch').value.toLowerCase();
      const rows = document.querySelectorAll('#branchTable tbody tr');
      rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(input) ? '' : 'none';
      });
    }

    function viewDetails(branch) {
      const html = `
        <p><strong>ID:</strong> ${branch.Branch_Id}</p>
        <p><strong>Name:</strong> ${branch.Branch_Name}</p>
        <p><strong>Status:</strong> ${branch.Is_Active == 1 ? 'Active' : 'Inactive'}</p>
      `;
      document.getElementById('popupDetails').innerHTML = html;
      document.getElementById('popupOverlay').style.display = 'flex';
    }

    function closePopup() {
      document.getElementById('popupOverlay').style.display = 'none';
    }

    function deleteBranch(id) {
      if (confirm("Are you sure you want to delete this branch?")) {
        window.location.href = "?delete=" + id;
      }
    }

    function toggleSidebar() {
      const sidebar = document.getElementById("sidebarMenu");
      sidebar.classList.toggle("mobile-hidden");
      sidebar.classList.toggle("mobile-visible");
    }
  </script>
</body>
</html>
