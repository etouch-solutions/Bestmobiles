<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

// ---------- Handle form submit (insert / update) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['staff_id']) && $_POST['staff_id'] !== '' ? intval($_POST['staff_id']) : null;
    $name = trim($_POST['staff_name'] ?? '');
    $cno = trim($_POST['staff_cno'] ?? '');
    $email = trim($_POST['staff_email'] ?? '');
    $address = trim($_POST['staff_address'] ?? '');
    $designation = trim($_POST['staff_designation'] ?? '');
    $status = isset($_POST['staff_status']) ? intval($_POST['staff_status']) : 0;
    $branch_id = isset($_POST['branch_id']) ? intval($_POST['branch_id']) : 0;

    // basic validation
    if ($name === '' || $cno === '') {
        header("Location: add_staff.php?error=1&msg=" . urlencode("Name and Contact are required"));
        exit();
    }

    if ($id) {
        $stmt = $conn->prepare(
            "UPDATE Staff_Master 
             SET Staff_Name=?, Staff_CNo=?, Staff_Email=?, Staff_Address=?, Staff_Designation=?, Staff_Status=?, Branch_Id=? 
             WHERE Staff_Id=?"
        );
        $stmt->bind_param("sssssiii", $name, $cno, $email, $address, $designation, $status, $branch_id, $id);
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO Staff_Master (Staff_Name, Staff_CNo, Staff_Email, Staff_Address, Staff_Designation, Staff_Status, Branch_Id) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssssii", $name, $cno, $email, $address, $designation, $status, $branch_id);
    }

    if (!$stmt) {
        header("Location: add_staff.php?error=1&msg=" . urlencode("DB prepare failed: " . $conn->error));
        exit();
    }

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: add_staff.php?success=1&msg=" . urlencode($id ? "Staff updated successfully" : "Staff added successfully"));
        exit();
    } else {
        $err = $stmt->error ?: $conn->error;
        $stmt->close();
        header("Location: add_staff.php?error=1&msg=" . urlencode("DB error: " . $err));
        exit();
    }
}

// ---------- Handle delete ----------
if (isset($_GET['delete'])) {
    $delId = intval($_GET['delete']);
    if ($delId > 0 && $conn->query("DELETE FROM Staff_Master WHERE Staff_Id = $delId")) {
        header("Location: add_staff.php?success=1&msg=" . urlencode("Staff deleted successfully"));
        exit();
    } else {
        header("Location: add_staff.php?error=1&msg=" . urlencode("Failed to delete staff"));
        exit();
    }
}

// ---------- Fetch edit data (if requested) ----------
$editData = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    if ($editId > 0) {
        $res = $conn->query("SELECT * FROM Staff_Master WHERE Staff_Id = $editId");
        if ($res && $res->num_rows > 0) {
            $editData = $res->fetch_assoc();
        }
    }
}

// ---------- Fetch lists for page ----------
$staffs = $conn->query("
    SELECT s.*, b.Branch_Name 
    FROM Staff_Master s 
    LEFT JOIN Branch_Master b ON s.Branch_Id = b.Branch_Id 
    ORDER BY s.Staff_Id DESC
");
$branches = $conn->query("SELECT Branch_Id, Branch_Name FROM Branch_Master ORDER BY Branch_Name ASC");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Staff Master</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    /* tiny local tweaks so form looks okay if your styles.css missing something */
    .add-branch input, .add-branch textarea, .add-branch select { width:100%; padding:8px; margin:8px 0; border-radius:4px; }
    .add-branch button { padding:10px 14px; border-radius:6px; cursor:pointer; }
  </style>
</head>
<body>
  <!-- your header / sidebar here -->
  <div class="navtop">
    <div class="logo">LOGO</div>
    <h1>Best Mobile Insurance Software</h1>
    <div class="hamburger" onclick="toggleSidebar()">☰</div>
  </div>

  <div class="container">
    <!-- Sidebar -->
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
        <!-- Form Area -->
        <section class="add-branch">
          <h3><?= $editData ? "Edit Staff" : "Add Staff" ?></h3>
          <form method="POST" novalidate>
            <?php if ($editData): ?>
              <input type="hidden" name="staff_id" value="<?= intval($editData['Staff_Id']) ?>">
            <?php endif; ?>
            <input type="text" name="staff_name" placeholder="Staff Name" required value="<?= htmlspecialchars($editData['Staff_Name'] ?? '') ?>">
            <input type="text" name="staff_cno" placeholder="Contact Number" required value="<?= htmlspecialchars($editData['Staff_CNo'] ?? '') ?>">
            <input type="email" name="staff_email" placeholder="Email" value="<?= htmlspecialchars($editData['Staff_Email'] ?? '') ?>">
            <textarea name="staff_address" placeholder="Address"><?= htmlspecialchars($editData['Staff_Address'] ?? '') ?></textarea>
            <input type="text" name="staff_designation" placeholder="Designation" value="<?= htmlspecialchars($editData['Staff_Designation'] ?? '') ?>">

            <select name="branch_id" required>
              <option value="0">-- Select Branch --</option>
              <?php while ($b = $branches->fetch_assoc()): 
                $selected = (isset($editData['Branch_Id']) && $editData['Branch_Id'] == $b['Branch_Id']) ? 'selected' : '';
              ?>
                <option value="<?= intval($b['Branch_Id']) ?>" <?= $selected ?>><?= htmlspecialchars($b['Branch_Name']) ?></option>
              <?php endwhile; ?>
            </select>

            <select name="staff_status">
              <option value="1" <?= (isset($editData['Staff_Status']) && $editData['Staff_Status'] == 1) ? 'selected' : '' ?>>Active</option>
              <option value="0" <?= (isset($editData['Staff_Status']) && $editData['Staff_Status'] == 0) ? 'selected' : '' ?>>Inactive</option>
            </select>

            <button type="submit"><?= $editData ? 'Update Staff' : 'Add Staff' ?></button>
          </form>
        </section>

        <!-- Staff List -->
        <section class="overview">
          <h3>Staff Overview</h3>
          <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="staffSearch" placeholder="Search staff..." onkeyup="filterStaff(this.value)">
          </div>

          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Branch</th>
                  <th>Designation</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $staffs->fetch_assoc()):
                  $jsonRow = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                  $statusText = $row['Staff_Status'] == 1 ? 'Active' : 'Inactive';
                  $rowClass = $row['Staff_Status'] == 1 ? 'active-row' : 'inactive-row';
                ?>
                  <tr class="<?= $rowClass ?>">
                    <td><?= htmlspecialchars($row['Staff_Name']) ?></td>
                    <td><?= htmlspecialchars($row['Branch_Name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['Staff_Designation']) ?></td>
                    <td><?= $statusText ?></td>
                    <td class="action-btns">
                      <i class='fas fa-eye' onclick='viewDetails(<?= $jsonRow ?>)'></i>
                      <a href='?edit=<?= intval($row['Staff_Id']) ?>'><i class='fas fa-pen'></i></a>
                      <a href='javascript:void(0)' onclick='deleteStaff(<?= intval($row['Staff_Id']) ?>)'><i class='fas fa-trash'></i></a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </main>
  </div>

  <!-- Include popup handler HERE (inside body) so header() can still work earlier. -->
  <?php include 'popup_handler.php'; ?>

  <!-- Popup view (detail) -->
  <div class="popup-overlay" id="popupOverlay" style="display:none;">
    <div class="popup-content" id="popupContent">
      <span class="close-btn" onclick="closePopup()">&times;</span>
      <h3>Staff Details</h3>
      <div id="popupDetails"></div>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebarMenu').classList.toggle('mobile-hidden');
    }

    function filterStaff(query) {
      const rows = document.querySelectorAll("tbody tr");
      rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(query.toLowerCase()) ? "" : "none";
      });
    }

    function viewDetails(staff) {
      const html = `
        <p><strong>ID:</strong> ${staff.Staff_Id}</p>
        <p><strong>Name:</strong> ${staff.Staff_Name}</p>
        <p><strong>Contact:</strong> ${staff.Staff_CNo}</p>
        <p><strong>Email:</strong> ${staff.Staff_Email}</p>
        <p><strong>Address:</strong> ${staff.Staff_Address}</p>
        <p><strong>Designation:</strong> ${staff.Staff_Designation}</p>
        <p><strong>Branch:</strong> ${staff.Branch_Name ?? 'Not Assigned'}</p>
        <p><strong>Status:</strong> ${staff.Staff_Status == 1 ? 'Active' : 'Inactive'}</p>
      `;
      document.getElementById("popupDetails").innerHTML = html;
      document.getElementById("popupOverlay").style.display = 'flex';
    }

    function closePopup() {
      document.getElementById("popupOverlay").style.display = 'none';
    }

    function deleteStaff(id) {
      if (confirm("Are you sure you want to delete this staff?")) {
        window.location.href = "?delete=" + id;
      }
    }
  </script>
</body>
</html>
